<?php

namespace App\Services;

use App\Jobs\SendBusinessUpgradePendingEmail;
use App\Models\Company;
use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentSynchronizer
{
    public function __construct(
        private MonerooPaymentService $moneroo,
        private LwsInstanceStorage $lws,
    ) {}

    public function synchronize(Payment $payment, ?string $monerooPaymentId = null): Payment
    {
        $transactionId = $monerooPaymentId ?: $payment->moneroo_payment_id;

        if (! filled($transactionId)) {
            throw new RuntimeException('Ce paiement ne possède aucun identifiant Moneroo.');
        }

        $remote = $this->moneroo->verify($transactionId);

        $notifyUpgrade = false;
        $synchronized = DB::transaction(function () use ($payment, $remote, $transactionId, &$notifyUpgrade): Payment {
            $locked = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $remoteId = (string) Arr::get($remote, 'id', '');

            if ($remoteId !== $transactionId) {
                throw new RuntimeException('L’identifiant du paiement vérifié ne correspond pas.');
            }

            $remoteStatus = strtolower((string) Arr::get($remote, 'status', ''));
            $status = $this->mapStatus($remoteStatus);

            if ($status === Payment::STATUS_PAID) {
                $this->assertSuccessfulPaymentMatches($locked, $remote);
            }

            if ($locked->isPaid() && $status !== Payment::STATUS_PAID) {
                $status = Payment::STATUS_PAID;
            }

            $locked->forceFill([
                'status' => $status,
                'verified_at' => now(),
                'paid_at' => $status === Payment::STATUS_PAID
                    ? ($locked->paid_at ?? now())
                    : $locked->paid_at,
                'failure_reason' => match ($status) {
                    Payment::STATUS_FAILED => (string) Arr::get($remote, 'capture.failure_message', 'Paiement refusé par le prestataire.'),
                    Payment::STATUS_EXPIRED => 'Le lien de paiement a expiré.',
                    default => null,
                },
                'provider_payload' => $this->auditPayload($remote),
            ])->save();

            if ($status === Payment::STATUS_PAID) {
                if ($locked->purpose === Payment::PURPOSE_UPGRADE
                    && $locked->upgrade_pending_notified_at === null) {
                    $locked->forceFill(['upgrade_pending_notified_at' => now()])->save();
                    $notifyUpgrade = true;
                }

                $this->applySubscriptionChange($locked);
            }

            return $locked->fresh();
        });

        if ($notifyUpgrade) {
            SendBusinessUpgradePendingEmail::dispatch($synchronized->id)
                ->onConnection('deferred');
        }

        return $synchronized;
    }

    private function applySubscriptionChange(Payment $payment): void
    {
        if ($payment->applied_at !== null
            || $payment->purpose !== Payment::PURPOSE_RENEWAL
            || $payment->duration_months === null
            || $payment->company_id === null) {
            return;
        }

        $company = Company::query()
            ->lockForUpdate()
            ->findOrFail($payment->company_id);
        $startsAt = $company->expires_at?->isFuture()
            ? $company->expires_at->copy()
            : now();

        if ($company->status === 'suspended') {
            $this->lws->reactivate($company);
        }

        $company->forceFill([
            'package' => $company->package,
            'status' => 'active',
            'suspension_reason' => null,
            'expires_at' => $startsAt->addMonthsNoOverflow($payment->duration_months),
            'subscription_started_at' => $company->subscription_started_at ?? now(),
        ])->save();

        $payment->forceFill(['applied_at' => now()])->save();
    }

    public function finalizeUpgrade(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment): Payment {
            $locked = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($locked->purpose !== Payment::PURPOSE_UPGRADE || ! $locked->isPaid()) {
                throw new RuntimeException('Seul un passage à BUSINESS payé peut être finalisé.');
            }

            if ($locked->applied_at !== null) {
                throw new RuntimeException('Cette évolution vers BUSINESS a déjà été finalisée.');
            }

            if ($locked->company_id === null || $locked->duration_months === null) {
                throw new RuntimeException('Ce paiement ne possède pas les informations nécessaires à la finalisation.');
            }

            $company = Company::query()->lockForUpdate()->findOrFail($locked->company_id);

            if (strtolower($company->package) !== 'start') {
                throw new RuntimeException('Seule une entreprise actuellement en offre START peut passer à BUSINESS.');
            }

            $startsAt = $company->expires_at?->isFuture()
                ? $company->expires_at->copy()
                : now();

            if ($company->status === 'suspended') {
                $this->lws->reactivate($company);
            }

            $company->forceFill([
                'package' => 'business',
                'status' => 'active',
                'suspension_reason' => null,
                'expires_at' => $startsAt->addMonthsNoOverflow($locked->duration_months),
                'subscription_started_at' => $company->subscription_started_at ?? now(),
            ])->save();

            $locked->forceFill([
                'applied_at' => now(),
                'upgrade_reviewed_at' => now(),
            ])->save();

            return $locked->fresh(['company']);
        });
    }

    /**
     * @param  array<string, mixed>  $remote
     */
    private function assertSuccessfulPaymentMatches(Payment $payment, array $remote): void
    {
        $remoteAmount = (int) round((float) Arr::get($remote, 'amount', 0));
        $remoteCurrency = strtoupper((string) (
            Arr::get($remote, 'currency.code')
            ?? Arr::get($remote, 'currency.iso_code')
            ?? Arr::get($remote, 'currency')
            ?? ''
        ));
        $metadataPaymentId = (string) Arr::get($remote, 'metadata.payment_id', '');
        $metadataReference = (string) Arr::get($remote, 'metadata.payment_reference', '');

        if ($remoteAmount < $payment->amount) {
            throw new RuntimeException('Le montant confirmé par Moneroo est inférieur au montant attendu.');
        }

        if ($remoteCurrency !== strtoupper($payment->currency)) {
            throw new RuntimeException('La devise confirmée par Moneroo ne correspond pas.');
        }

        if ($metadataPaymentId !== '' && $metadataPaymentId !== (string) $payment->id) {
            throw new RuntimeException('Les métadonnées Moneroo ne correspondent pas au paiement local.');
        }

        if ($metadataReference !== '' && $metadataReference !== $payment->reference) {
            throw new RuntimeException('La référence Moneroo ne correspond pas au paiement local.');
        }
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'success' => Payment::STATUS_PAID,
            'pending' => Payment::STATUS_PENDING,
            'failed' => Payment::STATUS_FAILED,
            'cancelled' => Payment::STATUS_CANCELLED,
            'expired' => Payment::STATUS_EXPIRED,
            'initiated' => Payment::STATUS_INITIATED,
            default => throw new RuntimeException('Statut Moneroo inconnu ou absent.'),
        };
    }

    /**
     * @param  array<string, mixed>  $remote
     * @return array<string, mixed>
     */
    private function auditPayload(array $remote): array
    {
        return Arr::only($remote, [
            'id',
            'status',
            'amount',
            'currency',
            'amount_formatted',
            'environment',
            'initiated_at',
            'metadata',
            'method',
            'gateway',
        ]);
    }
}
