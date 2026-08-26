<?php

namespace App\Jobs;

use App\Mail\PaymentLinkMail;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPaymentLinkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $paymentId) {}

    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);

        if ($payment === null || ! $payment->canSendLink()) {
            return;
        }

        try {
            Mail::to($payment->customer_email)->send(new PaymentLinkMail($payment));
            $payment->forceFill(['link_sent_at' => now()])->save();
        } catch (Throwable $exception) {
            Log::error('PAYMENT_LINK_MAIL_FAILED', [
                'payment_id' => $this->paymentId,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
