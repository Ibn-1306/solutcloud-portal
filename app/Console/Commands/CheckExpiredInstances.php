<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\LwsInstanceStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredInstances extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'solutcloud:check-expired';

    // La description de ce que fait le robot
    protected $description = 'Vérifie les échéances et suspend les instances expirées via FTP';

    public function handle(LwsInstanceStorage $lws): int
    {
        $this->info('Lancement du scan des instances...');

        // 1. On cherche les entreprises actives dont la date d'expiration est passée
        $expiredCompanies = Company::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredCompanies->isEmpty()) {
            $this->info('Aucune instance expirée détectée.');

            return self::SUCCESS;
        }

        foreach ($expiredCompanies as $company) {
            $this->warn("Suspension de : {$company->name} ({$company->instance_url})");

            try {
                // 2. Action FTP : on localise et verrouille la vraie racine Dolibarr.
                $lws->block($company);

                // 3. Mise à jour de la base de données
                $company->update(['status' => 'suspended']);

                $this->info("✓ {$company->name} suspendue avec succès.");

                // On garde une trace dans les logs pour l'admin
                Log::info("AUTO-SUSPENSION : L'instance {$company->instance_url} a été coupée par le robot.");

            } catch (\Exception $e) {
                $this->error("Erreur FTP pour {$company->name} : ".$e->getMessage());
                Log::error("ÉCHEC AUTO-SUSPENSION : {$company->name}. Erreur : ".$e->getMessage());
            }
        }

        $this->info('Fin du scan.');

        return self::SUCCESS;
    }
}
