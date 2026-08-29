<?php

namespace App\Support;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

final class InternationalPhone
{
    public static function normalize(mixed $value, string $defaultRegion = 'CI'): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            $util = PhoneNumberUtil::getInstance();
            $number = $util->parse(trim($value), strtoupper($defaultRegion));

            if (! $util->isValidNumber($number)) {
                return null;
            }

            return $util->format($number, PhoneNumberFormat::E164);
        } catch (NumberParseException) {
            return null;
        }
    }
}
