<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCustomerEmail implements ValidationRule
{
    public function __construct(private readonly bool $allowUnassignedClient = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = mb_strtolower(trim((string) $value));

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user !== null
            && ! ($this->allowUnassignedClient && $user->isClient() && $user->company_id === null)) {
            $fail('Un compte SOLUTCLOUD existe déjà avec cette adresse e-mail. Veuillez choisir une autre adresse e-mail.');
        }
    }
}
