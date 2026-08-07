<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstanceReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    // J'ajoute les types devant chaque propriété pour supprimer le rouge
    public \App\Models\Company $company;
    public string $url;
    public string $login;
    public string $password;

    /**
     * Le constructeur reçoit les datas
     */
    public function __construct(\App\Models\Company $company, string $url, string $login, string $password)
    {
        $this->company = $company;
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
    }
    
    // ... reste du code (envelope et content)

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Votre instance SOLUTCLOUD est prête !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instance_ready',
        );
    }
}