<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_leads', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index();
            $table->string('fullname');
            $table->string('email')->index();
            $table->string('phone', 30)->nullable();
            $table->string('company_name')->nullable();
            $table->string('profile', 100)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_leads');
    }
};
