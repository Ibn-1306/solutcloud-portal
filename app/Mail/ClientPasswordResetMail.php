<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ClientPasswordResetMail extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(
            subject: "SOLUTCLOUD — {$this->user->name}, réinitialisez votre mot de passe",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client_password_reset',
            text: 'emails.text.client_password_reset',
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-password-reset',
            'X-Auto-Response-Suppress' => 'OOF, AutoReply',
            'X-Entity-Ref-ID' => 'solutcloud-password-reset-'.$this->user->id,
        ]);
    }
}
