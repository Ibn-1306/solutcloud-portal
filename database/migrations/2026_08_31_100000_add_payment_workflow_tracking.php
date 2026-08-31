<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->timestamp('upgrade_reviewed_at')->nullable()->index()->after('archived_at');
            $table->unique('website_lead_id');
        });

        Schema::create('payment_checkout_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('moneroo_payment_id')->unique();
            $table->text('checkout_url');
            $table->timestamp('initialized_at');
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_checkout_attempts');

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique(['website_lead_id']);
            $table->dropColumn('upgrade_reviewed_at');
        });
    }
};
