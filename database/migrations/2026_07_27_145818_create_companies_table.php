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
    Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('name');              // Nom de l'entreprise
        $table->string('subdomain')->unique(); // Le préfixe
        $table->string('custom_domain')->nullable(); // Pour le forfait PREMIUM
        
        // Forfaits et États
        $table->enum('package', ['start', 'business', 'premium'])->default('start');
        $table->enum('status', ['active', 'suspended'])->default('active');
        
        $table->timestamp('expires_at');     // Date d'échéance de l'abonnement
        $table->timestamps();
    });
    }
};
