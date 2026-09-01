<?php

namespace App\Services;

use App\Models\SubscriptionPlan;

class SubscriptionPricingService
{
    public function currency(): string
    {
        return strtoupper((string) config('services.moneroo.currency', 'XOF'));
    }

    public function amountFor(SubscriptionPlan $plan): int
    {
        if ($this->currency() === 'XOF') {
            return $plan->promo_price;
        }

        $monthlyAmount = (int) config(
            'services.moneroo.sandbox_monthly_amounts.'.strtolower($plan->package),
            10,
        );

        return $monthlyAmount * $plan->duration_months;
    }
}
