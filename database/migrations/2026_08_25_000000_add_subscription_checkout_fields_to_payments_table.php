<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->string('purpose', 30)->default('initial')->index()->after('description');
            $table->unsignedSmallInteger('duration_months')->nullable()->after('purpose');
            $table->timestamp('applied_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex(['purpose']);
            $table->dropColumn(['purpose', 'duration_months', 'applied_at']);
        });
    }
};
