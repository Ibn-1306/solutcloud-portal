<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demos', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('subdomain');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('erp_login');
            $table->string('erp_password');
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demos');
    }
};