<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\PaymentSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class VerifyMonerooPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $paymentId, public ?string $monerooPaymentId = null) {}

    public function handle(PaymentSynchronizer $synchronizer): void
    {
        $payment = Payment::find($this->paymentId);

        if ($payment === null) {
            return;
        }

        try {
            $synchronizer->synchronize($payment, $this->monerooPaymentId);
        } catch (Throwable $exception) {
            Log::error('MONEROO_PAYMENT_VERIFICATION_FAILED', [
                'payment_id' => $this->paymentId,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
