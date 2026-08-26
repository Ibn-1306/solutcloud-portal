<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('subscription_plans', 'promo_price')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->integer('promo_price')->after('duration_months');
            });
        }

        if (! Schema::hasColumn('subscription_plans', 'regular_price')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->integer('regular_price')->after('promo_price');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['promo_price', 'regular_price'],
            fn (string $column): bool => Schema::hasColumn('subscription_plans', $column),
        ));

        if ($columns !== []) {
            Schema::table('subscription_plans', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
