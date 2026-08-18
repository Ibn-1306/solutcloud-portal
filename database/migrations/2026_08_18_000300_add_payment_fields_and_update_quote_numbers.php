<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('payment_transaction_id')->nullable()->unique()->after('quote_number');
            $table->text('payment_url')->nullable()->after('payment_transaction_id');
            $table->timestamp('payment_initialized_at')->nullable()->after('payment_url');
            $table->timestamp('paid_at')->nullable()->after('sent_at');
        });

        DB::table('quotes')
            ->select(['id', 'quote_number'])
            ->orderBy('id')
            ->eachById(function (object $quote): void {
                if (! preg_match('/^DEV-(\d{4})-(\d+)$/', $quote->quote_number, $matches)) {
                    return;
                }

                DB::table('quotes')
                    ->where('id', $quote->id)
                    ->update([
                        'quote_number' => sprintf('DEVIS-%s-%04d', substr($matches[1], -2), (int) $matches[2]),
                    ]);
            });

        Schema::table('quotes', function (Blueprint $table) {
            $table->unique('quote_number');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique(['quote_number']);
        });

        DB::table('quotes')
            ->select(['id', 'quote_number'])
            ->orderBy('id')
            ->eachById(function (object $quote): void {
                if (! preg_match('/^DEVIS-(\d{2})-(\d+)$/', $quote->quote_number, $matches)) {
                    return;
                }

                DB::table('quotes')
                    ->where('id', $quote->id)
                    ->update([
                        'quote_number' => sprintf('DEV-20%s-%04d', $matches[1], (int) $matches[2]),
                    ]);
            });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique(['payment_transaction_id']);
            $table->dropColumn(['payment_transaction_id', 'payment_url', 'payment_initialized_at', 'paid_at']);
        });
    }
};
