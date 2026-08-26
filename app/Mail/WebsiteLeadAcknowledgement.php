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
        $offer = $this->lead->offer ? ' '.$this->lead->offer : '';
        $subject = match ($this->lead->type) {
            'trial' => 'SOLUTCLOUD — Confirmation de votre demande de test',
            'order' => 'SOLUTCLOUD — Confirmation de votre commande'.$offer,
            'quote' => 'SOLUTCLOUD — Confirmation de votre demande de devis'.$offer,
            default => 'SOLUTCLOUD — Votre message est bien reçu',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.website_lead_acknowledgement');
    }
}
