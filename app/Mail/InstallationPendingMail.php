<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class InstallationPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Company $company) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "SOLUTCLOUD — Installation de {$this->company->name} en préparation",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.installation_pending',
            text: 'emails.text.installation_pending',
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-instance-installation',
            'X-Entity-Ref-ID' => 'solutcloud-installation-'.$this->company->id,
        ]);
    }
}
