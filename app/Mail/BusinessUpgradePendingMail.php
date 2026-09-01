<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessUpgradePendingMail extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(
            subject: 'SOLUTCLOUD — Votre passage à BUSINESS est en cours de traitement',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.business_upgrade_pending',
            text: 'emails.text.business_upgrade_pending',
        );
    }
}
