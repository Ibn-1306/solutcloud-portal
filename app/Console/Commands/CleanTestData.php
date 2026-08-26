<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanTestData extends Command
{
    protected $signature = 'solutcloud:clean-test-data
        {--admin-email=sales@i-solutions.ci : Compte administrateur unique à conserver}
        {--confirm-database= : Nom exact de la base ciblée}';

    protected $description = 'Supprime les données de test en conservant l’administrateur, les plans et les migrations';

    public function handle(): int
    {
        $environment = app()->environment();
        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");
        $confirmedDatabase = (string) $this->option('confirm-database');
        $adminEmail = mb_strtolower(trim((string) $this->option('admin-email')));

        if (! in_array($environment, ['local', 'production', 'testing'], true)) {
            $this->error("Nettoyage refusé dans l’environnement {$environment}.");

            return self::FAILURE;
        }

        if ($connection !== 'mysql' || $database === '') {
            $this->error("Nettoyage refusé : connexion inattendue {$connection}/{$database}.");

            return self::FAILURE;
        }

        if ($confirmedDatabase === '' || ! hash_equals($database, $confirmedDatabase)) {
            $this->error("Nettoyage refusé. Confirmez exactement la base avec --confirm-database={$database}");

            return self::FAILURE;
        }

        $admins = User::query()->where('role', User::ROLE_ADMIN)->get();

        if ($admins->count() !== 1 || mb_strtolower($admins->first()->email) !== $adminEmail) {
            $this->error('Nettoyage refusé : le compte administrateur unique ne correspond pas.');

            return self::FAILURE;
        }

        $admin = $admins->first();
        $tablesToEmpty = [
            'notifications',
            'personal_access_tokens',
            'password_reset_tokens',
            'sessions',
            'payments',
            'website_leads',
            'demos',
            'orders',
            'quotes',
            'newsletter_subscribers',
            'failed_jobs',
            'jobs',
            'job_batches',
            'cache_locks',
            'cache',
        ];
        $counts = [];

        foreach ($tablesToEmpty as $table) {
            if (Schema::hasTable($table)) {
                $counts[$table] = DB::table($table)->count();
            }
        }

        $counts['client_users'] = User::query()->whereKeyNot($admin->getKey())->count();
        $counts['companies'] = Schema::hasTable('companies') ? DB::table('companies')->count() : 0;

        $this->warn("Base ciblée : {$database} ({$environment})");
        $this->line("Administrateur conservé : {$admin->email} #{$admin->getKey()}");

        DB::transaction(function () use ($admin, $tablesToEmpty): void {
            foreach ($tablesToEmpty as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            User::query()->whereKeyNot($admin->getKey())->delete();

            if (Schema::hasTable('companies')) {
                DB::table('companies')->delete();
            }
        });

        $autoIncrementTables = [
            'companies',
            'demos',
            'failed_jobs',
            'jobs',
            'newsletter_subscribers',
            'orders',
            'payments',
            'personal_access_tokens',
            'quotes',
            'users',
            'website_leads',
        ];

        foreach ($autoIncrementTables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
            }
        }

        $deleted = array_sum($counts);
        $this->info("Nettoyage terminé : {$deleted} enregistrement(s) supprimé(s).");
        $this->line('Conservés : 1 administrateur, '.DB::table('subscription_plans')->count().' plans, '.DB::table('migrations')->count().' migrations.');

        return self::SUCCESS;
    }
}
