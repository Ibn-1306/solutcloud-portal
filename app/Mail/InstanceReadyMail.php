<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstanceReadyMail extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    // J'ajoute les types devant chaque propriété pour supprimer le rouge
    public Company $company;

    public string $url;

    public string $login;

    public string $password;

    /**
     * Le constructeur reçoit les datas
     */
    public function __construct(Company $company, string $url, string $login, string $password)
    {
        $this->company = $company;
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
    }

    // ... reste du code (envelope et content)

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(
            subject: 'SOLUTCLOUD — Votre instance est opérationnelle',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instance_ready',
            text: 'emails.text.instance_ready',
        );
    }
}
