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
        $table->string('erp_login')->nullable()->after('package');
        $table->string('erp_password')->nullable()->after('erp_login');
        $table->string('status')->default('active')->after('erp_password'); // active, suspended
        $table->timestamp('expires_at')->nullable()->after('status');
    });
    }

    public function down(): void
    {
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['erp_login', 'erp_password', 'status', 'expires_at']);
    });
    }
};
