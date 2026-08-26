<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Support\OfferCatalog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class AccountInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $resetUrl;

    /**
     * @var array{label: string, audience: string, details: array<int, array{label: string, value: string}>}
     */
    public array $offerDetails;

    /**
     * Création du mail d'invitation compte client.
     */
    public function __construct(
        User $user,
        string $resetUrl,
        public Company $company,
        public ?Payment $payment = null,
    ) {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->offerDetails = OfferCatalog::details($company->package);
    }

    /**
     * Sujet du mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "SOLUTCLOUD — {$this->user->name}, activez votre espace client",
        );
    }

    /**
     * Vue utilisée pour le contenu.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account_invitation',
            text: 'emails.text.account_invitation',
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-account-activation',
            'X-Entity-Ref-ID' => 'solutcloud-account-'.$this->user->id,
        ]);
    }

    /**
     * Pas de pièce jointe.
     */
    public function attachments(): array
    {
        return [];
    }
}
