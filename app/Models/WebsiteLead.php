<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class WebsiteLead extends Model
{
    protected $fillable = [
        'type',
        'fullname',
        'email',
        'phone',
        'company_name',
        'profile',
        'offer',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function commercialReference(): string
    {
        $prefix = $this->type === 'quote' ? 'DEVIS-REQ' : 'CMD';
        $year = $this->created_at?->format('y') ?? now()->format('y');

        return sprintf('%s-%s-%04d', $prefix, $year, $this->getKey());
    }

    public function commercialTypeLabel(): string
    {
        return $this->type === 'quote' ? 'Demande de devis' : 'Commande';
    }

    public function clientNotes(): ?string
    {
        $message = trim((string) $this->message);

        if ($message === '') {
            return null;
        }

        if (preg_match('/(?:^|\R)\s*Précisions\s*:\s*(.*)\z/isu', $message, $matches) === 1) {
            $notes = trim($matches[1]);

            return $notes !== '' ? $notes : null;
        }

        $isGeneratedOrderText = $this->type === 'order'
            && preg_match('/^Commande(?:\s+de\s+l[’\']offre)?\s+SOLUTCLOUD\s+(?:START|BUSINESS)\.?$/iu', $message) === 1;
        $isGeneratedQuoteText = $this->type === 'quote'
            && preg_match('/^Demande\s+de\s+devis(?:\s+pour)?(?:\s+SOLUTCLOUD)?\s+PREMIUM\.?$/iu', $message) === 1;

        return ($isGeneratedOrderText || $isGeneratedQuoteText) ? null : $message;
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeTrials(Builder $query): Builder
    {
        return $query->where('type', 'trial');
    }

    public static function pendingTrialRequests(): Collection
    {
        $demoEmails = Demo::query()
            ->pluck('email')
            ->map(fn (string $email): string => mb_strtolower(trim($email)))
            ->filter()
            ->flip();

        return static::query()
            ->trials()
            ->latest()
            ->get()
            ->reject(fn (self $lead): bool => $demoEmails->has(mb_strtolower(trim($lead->email))))
            ->values();
    }
}
