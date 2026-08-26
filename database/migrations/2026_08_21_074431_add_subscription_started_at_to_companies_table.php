<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->timestamp('subscription_started_at')
                ->nullable()
                ->after('expires_at');

        });
    }


    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->dropColumn('subscription_started_at');

        });
    }
};
