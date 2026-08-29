<?php

namespace Tests\Unit;

use App\Support\InternationalPhone;
use PHPUnit\Framework\TestCase;

class InternationalPhoneTest extends TestCase
{
    public function test_it_normalizes_valid_numbers_from_different_countries(): void
    {
        $this->assertSame('+2250708091011', InternationalPhone::normalize('+225 07 08 09 10 11'));
        $this->assertSame('+33612345678', InternationalPhone::normalize('+33 6 12 34 56 78'));
    }

    public function test_it_rejects_an_invalid_number(): void
    {
        $this->assertNull(InternationalPhone::normalize('+225 12 34'));
    }
}
