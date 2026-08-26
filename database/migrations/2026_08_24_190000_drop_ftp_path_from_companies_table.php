<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('companies', 'ftp_path')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropColumn('ftp_path');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('companies', 'ftp_path')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->string('ftp_path')->nullable()->after('custom_domain');
            });
        }
    }
};
