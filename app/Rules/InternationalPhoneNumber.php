<?php

namespace App\Rules;

use App\Support\InternationalPhone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InternationalPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (InternationalPhone::normalize($value) === null) {
            $fail('Le numéro de téléphone n’est pas valide pour le pays sélectionné.');
        }
    }
}
