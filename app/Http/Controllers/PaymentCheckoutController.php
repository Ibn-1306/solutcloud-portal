<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentLinkExpiredException;
use App\Models\Payment;
use App\Models\PaymentCheckoutAttempt;
use App\Services\PaymentSynchronizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentCheckoutController extends Controller
{
    public function __invoke(PaymentCheckoutAttempt $attempt, PaymentSynchronizer $synchronizer): Response|RedirectResponse
    {
        $payment = $attempt->payment;

        if ($payment === null || $attempt->superseded_at !== null || $this->hasTimedOut($attempt)) {
            $this->expire($payment, $attempt);

            return $this->expiredResponse($payment);
        }

        if ($payment->isPaid()) {
            return redirect()->route('payments.return', ['paymentId' => $attempt->moneroo_payment_id]);
        }

        if ($payment->isExpired()) {
            return $this->expiredResponse($payment);
        }

        try {
            $payment = $synchronizer->synchronize($payment, $attempt->moneroo_payment_id);
        } catch (PaymentLinkExpiredException) {
            $this->expire($payment, $attempt);

            return $this->expiredResponse($payment->fresh());
        } catch (Throwable $exception) {
            Log::warning('MONEROO_CHECKOUT_PRECHECK_FAILED', [
                'payment_id' => $payment->id,
                'attempt_id' => $attempt->id,
                'message' => $exception->getMessage(),
            ]);
        }

        if ($payment->isPaid()) {
            return redirect()->route('payments.return', ['paymentId' => $attempt->moneroo_payment_id]);
        }

        if ($payment->isExpired()) {
            return $this->expiredResponse($payment);
        }

        return redirect()->away($attempt->checkout_url);
    }

    private function hasTimedOut(PaymentCheckoutAttempt $attempt): bool
    {
        $ttlMinutes = max(1, (int) config('services.moneroo.checkout_ttl_minutes', 1440));

        return $attempt->initialized_at?->copy()->addMinutes($ttlMinutes)->isPast() ?? true;
    }

    private function expire(?Payment $payment, PaymentCheckoutAttempt $attempt): void
    {
        if ($payment === null || $payment->isPaid() || $payment->moneroo_payment_id !== $attempt->moneroo_payment_id) {
            return;
        }

        $payment->forceFill([
            'status' => Payment::STATUS_EXPIRED,
            'verified_at' => now(),
            'failure_reason' => 'Le lien de paiement a expiré.',
        ])->save();
    }

    private function expiredResponse(?Payment $payment): Response
    {
        return response()
            ->view('payments.expired', ['payment' => $payment], 404)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
