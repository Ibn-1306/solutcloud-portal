<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class InstanceInstallationMail extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Company $company,
        public ?Payment $payment,
        public ?string $activationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(
            subject: "SOLUTCLOUD — {$this->user->name}, votre instance est en cours d’installation",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instance_installation',
            text: 'emails.text.instance_installation',
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-instance-installation-activation',
            'X-Auto-Response-Suppress' => 'OOF, AutoReply',
            'X-Entity-Ref-ID' => 'solutcloud-instance-installation-'.$this->company->id,
        ]);
    }
}
