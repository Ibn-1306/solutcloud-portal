<?php

namespace App\Mail;

use App\Models\WebsiteLead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteLeadAcknowledgement extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WebsiteLead $lead) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->lead->type) {
            'trial' => 'SOLUTCLOUD — Votre demande d’essai est bien reçue',
            'quote' => 'SOLUTCLOUD — Votre demande de devis est bien reçue',
            default => 'SOLUTCLOUD — Votre message est bien reçu',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.website_lead_acknowledgement');
    }
}
