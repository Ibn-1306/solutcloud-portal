<?php

namespace App\Mail;

use App\Models\WebsiteLead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteLeadReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WebsiteLead $lead) {}

    public function envelope(): Envelope
    {
        $label = match ($this->lead->type) {
            'trial' => 'Demande d’essai',
            'quote' => 'Demande de devis',
            default => 'Message de contact',
        };

        return new Envelope(
            subject: "SOLUTCLOUD — {$label} — {$this->lead->fullname}",
            replyTo: [new Address($this->lead->email, $this->lead->fullname)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.website_lead_received');
    }
}
