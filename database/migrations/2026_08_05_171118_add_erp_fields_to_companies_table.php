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
    // Sécurité : ne pas ajouter les colonnes si elles existent déjà
    // (status et expires_at sont déjà créées dans create_companies_table)
    Schema::table('companies', function (Blueprint $table) {
        if (!Schema::hasColumn('companies', 'erp_login')) {
            $table->string('erp_login')->nullable()->after('package');
        }
        if (!Schema::hasColumn('companies', 'erp_password')) {
            $table->string('erp_password')->nullable()->after('erp_login');
        }
        if (!Schema::hasColumn('companies', 'status')) {
            $table->string('status')->default('active')->after('erp_password');
        }
        if (!Schema::hasColumn('companies', 'expires_at')) {
            $table->timestamp('expires_at')->nullable()->after('status');
        }
    });
    }

    public function down(): void
    {
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['erp_login', 'erp_password', 'status', 'expires_at']);
    });
    }
};
