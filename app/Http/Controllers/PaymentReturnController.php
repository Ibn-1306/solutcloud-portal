<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentLinkExpiredException;
use App\Models\Payment;
use App\Models\PaymentCheckoutAttempt;
use App\Services\PaymentSynchronizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentReturnController extends Controller
{
    public function __invoke(Request $request, PaymentSynchronizer $synchronizer): View|RedirectResponse|Response
    {
        $monerooPaymentId = (string) $request->query('paymentId', '');
        $payment = $monerooPaymentId !== ''
            ? Payment::where('moneroo_payment_id', $monerooPaymentId)->first()
            : null;
        $attempt = $monerooPaymentId !== ''
            ? PaymentCheckoutAttempt::query()->where('moneroo_payment_id', $monerooPaymentId)->first()
            : null;
        $payment ??= $attempt?->payment;

        if ($payment === null) {
            return $this->expiredResponse();
        }

        if ($attempt?->superseded_at !== null || $payment->isExpired()) {
            return $this->expiredResponse($payment);
        }

        try {
            if (! $payment->isPaid()) {
                $payment = $synchronizer->synchronize($payment, $monerooPaymentId);
            }
        } catch (PaymentLinkExpiredException) {
            $payment->forceFill([
                'status' => Payment::STATUS_EXPIRED,
                'verified_at' => now(),
                'failure_reason' => 'Le lien de paiement a expiré.',
            ])->save();

            return $this->expiredResponse($payment);
        } catch (Throwable $exception) {
            Log::error('MONEROO_PAYMENT_RETURN_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return $this->unconfirmedRedirect($payment);
        }

        if (! $payment->isPaid()) {
            if ($payment->isExpired()) {
                return $this->expiredResponse($payment);
            }

            return $this->unconfirmedRedirect($payment);
        }

        return view('payments.success', [
            'payment' => $payment,
            'subscriptionUpdated' => $payment->purpose === Payment::PURPOSE_RENEWAL,
            'upgradePending' => $payment->purpose === Payment::PURPOSE_UPGRADE
                && $payment->applied_at === null,
            'upgradeFinalized' => $payment->purpose === Payment::PURPOSE_UPGRADE
                && $payment->applied_at !== null,
        ]);
    }

    private function unconfirmedRedirect(Payment $payment): RedirectResponse
    {
        $route = in_array($payment->purpose, [Payment::PURPOSE_RENEWAL, Payment::PURPOSE_UPGRADE], true)
            ? 'client.renew'
            : 'login';

        return redirect()->route($route)->withErrors([
            'payment' => 'Le paiement n’a pas encore été confirmé. Vous pouvez réessayer depuis votre espace client ou contacter le support.',
        ]);
    }

    private function expiredResponse(?Payment $payment = null): Response
    {
        return response()
            ->view('payments.expired', ['payment' => $payment], 404)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
