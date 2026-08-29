<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(
            subject: "SOLUTCLOUD — Règlement {$this->payment->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_link',
            text: 'emails.text.payment_link',
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-payment-link',
            'X-Auto-Response-Suppress' => 'OOF, AutoReply',
            'X-Entity-Ref-ID' => 'solutcloud-payment-'.$this->payment->reference,
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
