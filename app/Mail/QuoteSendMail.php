<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class QuoteSendMail extends Mailable
{
    use Queueable, SerializesModels;

    public Quote $quote;

    public function __construct(Quote $quote)
    {
        if (! is_string($quote->payment_url)
            || filter_var($quote->payment_url, FILTER_VALIDATE_URL) === false
            || parse_url($quote->payment_url, PHP_URL_SCHEME) !== 'https') {
            throw new InvalidArgumentException('Le devis doit disposer d’un lien de paiement HTTPS valide avant son envoi.');
        }

        $this->quote = $quote;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SOLUTCLOUD — Devis PREMIUM '.$this->quote->quote_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote_send',
        );
    }
}
