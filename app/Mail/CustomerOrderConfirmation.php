<?php

namespace App\Mail;

use App\Models\Order; // IMPORTANT : On importe le modèle
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    // IMPORTANT : On déclare la variable pour qu'elle soit accessible dans Blade
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Sujet pro qui rassure le client
            subject: '🚀 SOLUTCLOUD : Confirmation de votre commande',
        );
    }

    public function content(): Content
    {
        return new Content(
            // On pointe vers ton template de confirmation (pas encore celui des accès)
            view: 'emails.client_access', 
        );
    }

    public function attachments(): array
    {
        return [];
    }
}