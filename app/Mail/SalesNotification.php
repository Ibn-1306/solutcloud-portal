<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalesNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * L'instance de la commande.
     * Déclarer en "public" permet d'y accéder directement dans le template Blade.
     *
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Définit l'enveloppe du message (Sujet, expéditeur, etc.)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SOLUTCLOUD] Nouvelle commande à traiter — '.$this->order->company_name,
        );
    }

    /**
     * Définit le contenu du message (La vue Blade)
     */
    public function content(): Content
    {
        return new Content(
            // Doit correspondre à resources/views/emails/admin_copy.blade.php
            view: 'emails.admin_copy',
        );
    }

    /**
     * Pièces jointes (si nécessaire)
     */
    public function attachments(): array
    {
        return [];
    }
}
