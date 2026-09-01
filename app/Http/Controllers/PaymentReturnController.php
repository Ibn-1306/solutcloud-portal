<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentSynchronizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentReturnController extends Controller
{
    public function __invoke(Request $request, PaymentSynchronizer $synchronizer): View|RedirectResponse
    {
        $monerooPaymentId = (string) $request->query('paymentId', '');
        $payment = $monerooPaymentId !== ''
            ? Payment::where('moneroo_payment_id', $monerooPaymentId)->first()
            : null;

        if ($payment === null) {
            return redirect()->route('login')->withErrors([
                'payment' => 'Le paiement retourné par Moneroo est introuvable.',
            ]);
        }

        try {
            if (! $payment->isPaid()) {
                $payment = $synchronizer->synchronize($payment);
            }
        } catch (Throwable $exception) {
            Log::error('MONEROO_PAYMENT_RETURN_FAILED', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return $this->unconfirmedRedirect($payment);
        }

        if (! $payment->isPaid()) {
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
}
