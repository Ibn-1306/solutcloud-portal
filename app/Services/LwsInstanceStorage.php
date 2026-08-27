<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LwsInstanceStorage
{
    private const BACKUP_FILENAME = '.htaccess.solutcloud-backup';

    private const SUSPENSION_MARKER = '# SOLUTCLOUD INSTANCE SUSPENDED';

    public function resolvePath(Company $company): string
    {
        $candidates = $company->ftpPathCandidates();

        if ($candidates === []) {
            throw new RuntimeException('Aucun domaine LWS n\'est configuré pour cette instance.');
        }

        foreach ($candidates as $candidate) {
            if ($this->isDolibarrRoot($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException(
            'Racine Dolibarr introuvable. Emplacements vérifiés : '.implode(', ', $candidates).'.'
        );
    }

    public function suspend(Company $company): string
    {
        $path = $this->resolvePath($company);
        $suspensionUrl = rtrim(
            (string) config('services.solutcloud.portal_url', 'https://login.solutcloud.com'),
            '/',
        ).'/abonnement-expire?'.http_build_query([
            // Un nom d'hôte simple évite qu'Apache/LWS altère les caractères
            // encodés de "https://" dans la cible d'une RewriteRule.
            'instance' => (string) parse_url($company->instance_url, PHP_URL_HOST),
        ], '', '&', PHP_QUERY_RFC3986);
        $content = self::SUSPENSION_MARKER."\n"
            ."<IfModule mod_headers.c>\n"
            ."    Header always set Cache-Control \"no-store, no-cache, must-revalidate, max-age=0\"\n"
            ."    Header always set Pragma \"no-cache\"\n"
            ."    Header always set Expires \"0\"\n"
            ."</IfModule>\n"
            ."RewriteEngine On\n"
            .'RewriteRule ^ '.str_replace(' ', '%20', $suspensionUrl).' [L,R=302]';

        $this->writeLock($path, $content);

        return $path;
    }

    public function block(Company $company): string
    {
        $path = $this->resolvePath($company);
        $content = self::SUSPENSION_MARKER."\n"
            ."<IfModule mod_authz_core.c>\n"
            ."    Require all denied\n"
            ."</IfModule>\n"
            ."<IfModule !mod_authz_core.c>\n"
            ."    Order Deny,Allow\n"
            ."    Deny from all\n"
            .'</IfModule>';

        $this->writeLock($path, $content);

        return $path;
    }

    public function reactivate(Company $company): string
    {
        $path = $this->resolvePath($company);
        $disk = $this->disk();
        $htaccess = $path.'/.htaccess';
        $backup = $path.'/'.self::BACKUP_FILENAME;

        if ($disk->exists($backup)) {
            $originalContent = $disk->get($backup);
            $this->putAndVerify($htaccess, $originalContent);

            if (! $disk->delete($backup) || $disk->exists($backup)) {
                throw new RuntimeException('Le verrou a été retiré, mais sa sauvegarde FTP n\'a pas pu être nettoyée.');
            }
        } elseif ($disk->exists($htaccess)) {
            $currentContent = $disk->get($htaccess);

            if (! str_contains($currentContent, self::SUSPENSION_MARKER)) {
                throw new RuntimeException('Le fichier .htaccess présent ne provient pas de SOLUTCLOUD et ne sera pas supprimé.');
            }

            if (! $disk->delete($htaccess) || $disk->exists($htaccess)) {
                throw new RuntimeException('Le verrou FTP de suspension n\'a pas pu être supprimé.');
            }
        }

        return $path;
    }

    private function isDolibarrRoot(string $path): bool
    {
        $disk = $this->disk();

        return $disk->exists($path.'/index.php')
            && ($disk->exists($path.'/main.inc.php') || $disk->exists($path.'/conf/conf.php'));
    }

    private function writeLock(string $path, string $content): void
    {
        $disk = $this->disk();
        $htaccess = $path.'/.htaccess';
        $backup = $path.'/'.self::BACKUP_FILENAME;

        if ($disk->exists($htaccess)) {
            $currentContent = $disk->get($htaccess);

            if (! str_contains($currentContent, self::SUSPENSION_MARKER) && ! $disk->exists($backup)) {
                $this->putAndVerify($backup, $currentContent);
            }
        }

        $this->putAndVerify($htaccess, $content);
    }

    private function putAndVerify(string $path, string $content): void
    {
        $disk = $this->disk();

        if (! $disk->put($path, $content)) {
            throw new RuntimeException('Écriture FTP impossible pour '.$path.'.');
        }

        if (! $disk->exists($path) || trim($disk->get($path)) !== trim($content)) {
            throw new RuntimeException('La modification FTP n\'a pas pu être vérifiée pour '.$path.'.');
        }
    }

    private function disk(): FilesystemAdapter
    {
        return Storage::disk('lws');
    }
}
