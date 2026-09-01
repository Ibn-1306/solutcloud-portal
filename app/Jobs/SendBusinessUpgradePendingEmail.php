<?php

namespace App\Jobs;

use App\Mail\BusinessUpgradePendingMail;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBusinessUpgradePendingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $paymentId) {}

    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);

        if ($payment === null || ! $payment->isPaid() || $payment->purpose !== Payment::PURPOSE_UPGRADE) {
            return;
        }

        try {
            Mail::to($payment->customer_email)->send(new BusinessUpgradePendingMail($payment));
        } catch (Throwable $exception) {
            Log::error('BUSINESS_UPGRADE_PENDING_MAIL_FAILED', [
                'payment_id' => $this->paymentId,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
