<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('subscription_plans', 'price')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('subscription_plans', 'price')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->integer('price');
            });
        }
    }
};
