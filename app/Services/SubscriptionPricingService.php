<?php

namespace App\Services;

use App\Models\Company;
use App\Models\SubscriptionPlan;

class SubscriptionPricingService
{
    public function currency(): string
    {
        return strtoupper((string) config('services.moneroo.currency', 'XOF'));
    }

    public function amountFor(Company $company, SubscriptionPlan $plan): int
    {
        if ($this->currency() === 'XOF') {
            return $company->isPromoPeriod()
                ? $plan->promo_price
                : $plan->regular_price;
        }

        $monthlyAmount = (int) config(
            'services.moneroo.sandbox_monthly_amounts.'.strtolower($plan->package),
            10,
        );

        return $monthlyAmount * $plan->duration_months;
    }
}
