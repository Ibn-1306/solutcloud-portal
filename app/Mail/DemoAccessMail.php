<?php

namespace App\Mail;

use App\Models\Demo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public Demo $demo;

    public function __construct(Demo $demo)
    {
        $this->demo = $demo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SOLUTCLOUD — Vos accès de démonstration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo_access',
        );
    }
}