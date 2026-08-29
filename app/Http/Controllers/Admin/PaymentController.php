<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendPaymentLinkEmail;
use App\Models\Payment;
use App\Models\WebsiteLead;
use App\Rules\InternationalPhoneNumber;
use App\Services\MonerooPaymentService;
use App\Services\PaymentSynchronizer;
use App\Support\InternationalPhone;
use App\Support\OfferCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $paymentCurrency = $this->configuredCurrency();
        $defaultPaymentAmounts = $paymentCurrency === 'XOF'
            ? ['start' => 70800, 'business' => 118800, 'premium' => '']
            : ['start' => 10, 'business' => 20, 'premium' => ''];

        $payments = Payment::query()
            ->visibleInTracking()
            ->with(['websiteLead', 'company'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $commercialRequests = WebsiteLead::query()
            ->whereIn('type', ['order', 'quote'])
            ->latest()
            ->limit(100)
            ->get();

        $totalCount = Payment::visibleInTracking()->count();
        $paidCount = Payment::visibleInTracking()->paid()->count();
        $pendingCount = Payment::visibleInTracking()->whereIn('status', [
            Payment::STATUS_INITIATED,
            Payment::STATUS_PENDING,
        ])->count();
        $paidAmount = (int) Payment::visibleInTracking()->paid()
            ->where('currency', $paymentCurrency)
            ->sum('amount');
        $preselectedLeadId = $request->integer('lead');
        $offerCatalog = collect(['start', 'business', 'premium'])
            ->mapWithKeys(fn (string $package): array => [
                $package => OfferCatalog::details($package),
            ])
            ->all();

        return view('admin.payments.index', compact(
            'payments',
            'commercialRequests',
            'totalCount',
            'paidCount',
            'pendingCount',
            'paidAmount',
            'paymentCurrency',
            'defaultPaymentAmounts',
            'preselectedLeadId',
            'offerCatalog',
        ));
    }

    public function store(Request $request, MonerooPaymentService $moneroo): RedirectResponse
    {
        $paymentCurrency = $this->configuredCurrency();
        $minimumAmount = $paymentCurrency === 'XOF' ? 100 : 1;

        $data = $request->validate([
            'website_lead_id' => ['nullable', 'integer', 'exists:website_leads,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email:rfc', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30', new InternationalPhoneNumber],
            'company_name' => ['required', 'string', 'max:255'],
            'package' => ['required', Rule::in(['start', 'business', 'premium'])],
            'amount' => ['required', 'integer', 'min:'.$minimumAmount],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $commercialRequest = null;

        if (isset($data['website_lead_id'])) {
            $commercialRequest = WebsiteLead::query()
                ->whereKey($data['website_lead_id'])
                ->whereIn('type', ['order', 'quote'])
                ->first();

            if (! $commercialRequest) {
                return back()->withInput()->withErrors([
                    'website_lead_id' => 'La demande sélectionnée ne peut pas être associée à un paiement.',
                ]);
            }
        }

        // Pour une demande issue du site, les notes du paiement correspondent
        // exclusivement aux précisions saisies par le client. Les références
        // commerciales restent dans leurs champs dédiés.
        $data['description'] = $commercialRequest
            ? $commercialRequest->clientNotes()
            : (filled($data['description'] ?? null) ? trim((string) $data['description']) : null);

        $payment = Payment::create([
            ...$data,
            'customer_email' => mb_strtolower(trim($data['customer_email'])),
            'customer_phone' => InternationalPhone::normalize($data['customer_phone'] ?? null),
            'currency' => $paymentCurrency,
            'purpose' => Payment::PURPOSE_INITIAL,
            'status' => Payment::STATUS_DRAFT,
        ]);

        try {
            $this->initializeWithMoneroo($payment, $moneroo);

            return redirect()->route('admin.payments.index')
                ->with('status', "Paiement {$payment->reference} créé. Le lien sécurisé est envoyé au client.");
        } catch (Throwable $exception) {
            $payment->forceFill(['failure_reason' => $exception->getMessage()])->save();

            Log::error('MONEROO_PAYMENT_INITIALIZATION_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.payments.index')
                ->withErrors("Le paiement {$payment->reference} a été enregistré, mais Moneroo n’a pas créé le lien : {$exception->getMessage()}");
        }
    }

    public function initialize(Payment $payment, MonerooPaymentService $moneroo): RedirectResponse
    {
        if (filled($payment->moneroo_payment_id) || $payment->isPaid()) {
            return back()->withErrors('Ce paiement est déjà initialisé.');
        }

        try {
            $this->initializeWithMoneroo($payment, $moneroo);

            return back()->with('status', "Le lien {$payment->reference} a été créé et envoyé au client.");
        } catch (Throwable $exception) {
            $payment->forceFill(['failure_reason' => $exception->getMessage()])->save();

            return back()->withErrors($exception->getMessage());
        }
    }

    public function sendLink(Payment $payment): RedirectResponse
    {
        if (! $payment->canSendLink()) {
            return back()->withErrors('Ce lien de paiement ne peut plus être envoyé.');
        }

        SendPaymentLinkEmail::dispatch($payment->id)->onConnection('deferred');

        return back()->with('status', "Le lien {$payment->reference} est renvoyé au client.");
    }

    public function refresh(Payment $payment, PaymentSynchronizer $synchronizer): RedirectResponse
    {
        try {
            $synchronized = $synchronizer->synchronize($payment);

            return back()->with(
                'status',
                "Statut {$synchronized->reference} actualisé : {$synchronized->statusLabel()}.",
            );
        } catch (RuntimeException $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        if (! $payment->canRemoveFromTracking()) {
            return back()->withErrors(
                'Un paiement payé, déjà appliqué ou précédemment retiré ne peut pas être supprimé du suivi.',
            );
        }

        $reference = $payment->reference;
        $payment->forceFill(['archived_at' => now()])->save();

        return back()->with(
            'status',
            "Le paiement {$reference} a été supprimé du tableau de suivi.",
        );
    }

    private function initializeWithMoneroo(Payment $payment, MonerooPaymentService $moneroo): void
    {
        $initialized = $moneroo->initialize($payment);

        $payment->forceFill([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => $initialized['id'],
            'checkout_url' => $initialized['checkout_url'],
            'initialized_at' => now(),
            'failure_reason' => null,
        ])->save();

        SendPaymentLinkEmail::dispatch($payment->id)->onConnection('deferred');
    }

    private function configuredCurrency(): string
    {
        $currency = strtoupper((string) config('services.moneroo.currency', 'XOF'));

        return preg_match('/^[A-Z]{3}$/', $currency) === 1 ? $currency : 'XOF';
    }
}
