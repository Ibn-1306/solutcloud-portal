<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_id')->unique(); // ID unique de Moneroo
        $table->string('company_name');
        $table->string('customer_name');
        $table->string('customer_email');
        $table->string('customer_phone');
        $table->string('plan');
        $table->decimal('amount', 15, 2);
        $table->string('status')->default('pending'); // Statut initial
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
