<?php

namespace App\Mail;

use App\Mail\Concerns\HasSolutcloudDelivery;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcome extends Mailable
{
    use HasSolutcloudDelivery, Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return $this->solutcloudEnvelope(subject: 'Bienvenue dans la newsletter SOLUTCLOUD');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter_welcome',
            text: 'emails.text.newsletter_welcome',
        );
    }
}
