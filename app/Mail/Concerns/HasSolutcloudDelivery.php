<?php

namespace App\Mail\Concerns;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Support\Str;

trait HasSolutcloudDelivery
{
    /**
     * Apply the same authenticated sender identity to every SOLUTCLOUD email.
     *
     * @param  array<int, Address>  $replyTo
     */
    public function headers(): Headers
    {
        return new Headers(text: [
            'X-Mailin-Tag' => 'solutcloud-'.Str::kebab(class_basename($this)),
            'X-Auto-Response-Suppress' => 'OOF, AutoReply',
        ]);
    }

    protected function solutcloudEnvelope(string $subject, array $replyTo = []): Envelope
    {
        $fromAddress = (string) config('mail.from.address');
        $fromName = (string) config('mail.from.name', 'SOLUTCLOUD');

        if ($replyTo === []) {
            $replyTo = [new Address(
                (string) config('mail.reply_to.address', $fromAddress),
                (string) config('mail.reply_to.name', $fromName),
            )];
        }

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: $replyTo,
            subject: $subject,
        );
    }
}
