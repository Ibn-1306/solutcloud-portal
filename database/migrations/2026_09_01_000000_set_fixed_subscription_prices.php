<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->applyPrices([
            'START' => 5900,
            'BUSINESS' => 9900,
            'PREMIUM' => 24900,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            return;
        }

        $legacyMonthlyPrices = [
            'START' => 10000,
            'BUSINESS' => 18000,
            'PREMIUM' => 49800,
        ];

        foreach ($legacyMonthlyPrices as $package => $monthlyPrice) {
            DB::table('subscription_plans')
                ->where('package', $package)
                ->get(['id', 'duration_months'])
                ->each(function (object $plan) use ($monthlyPrice): void {
                    DB::table('subscription_plans')
                        ->where('id', $plan->id)
                        ->update(['regular_price' => $monthlyPrice * (int) $plan->duration_months]);
                });
        }
    }

    /** @param array<string, int> $monthlyPrices */
    private function applyPrices(array $monthlyPrices): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            return;
        }

        foreach ($monthlyPrices as $package => $monthlyPrice) {
            DB::table('subscription_plans')
                ->where('package', $package)
                ->get(['id', 'duration_months'])
                ->each(function (object $plan) use ($monthlyPrice): void {
                    $price = $monthlyPrice * (int) $plan->duration_months;

                    DB::table('subscription_plans')
                        ->where('id', $plan->id)
                        ->update([
                            'promo_price' => $price,
                            'regular_price' => $price,
                        ]);
                });
        }
    }
};
