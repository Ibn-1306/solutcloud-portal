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
        Schema::table('companies', function (Blueprint $table) {
            // On ajoute la colonne pour stocker le cumul des paiements (Chiffre d'affaires par client)
            // decimal(15,2) permet de stocker de gros montants sans erreur de calcul
            $table->decimal('total_paid', 15, 2)->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('total_paid');
        });
    }
};