<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail, DB, Log, Storage};

class CompanyController extends Controller 
{
    public function index() 
    {
    $companies = Company::latest()->get();

    // 1. Compter les instances actives
    $activeCount = Company::where('status', 'active')->count();

    // 2. Calculer le revenu mensuel estimé (MRR)
    // On additionne les prix selon le forfait de chaque client actif
    $monthlyRevenue = 0;
    foreach ($companies->where('status', 'active') as $company) {
        if ($company->package === 'start') $monthlyRevenue += 5900;
        if ($company->package === 'business') $monthlyRevenue += 9900;
        if ($company->package === 'premium') $monthlyRevenue += 24900;
    }

    // 3. Compter les alertes (Expire dans moins de 7 jours)
    $alerts = Company::where('status', 'active')
        ->where('expires_at', '<=', now()->addDays(7))
        ->where('expires_at', '>', now())
        ->count();

    return view('dashboard', [
        'companies' => $companies,
        'activeCount' => $activeCount,
        'monthlyRevenue' => $monthlyRevenue,
        'alerts' => $alerts
    ]);
    }

    public function store(Request $request) 
    {
        // 1. On ajuste la validation selon le forfait
        $isPremium = $request->package === 'premium';
        
        $data = $request->validate([
            'name' => 'required|string|max:255', 
            // Si premium, on autorise les points (regex), sinon alpha_dash
            'subdomain' => [
                'required', 
                'unique:companies,subdomain',
                $isPremium ? 'regex:/^[a-z0-9.-]+$/i' : 'alpha_dash'
            ],
            'email' => 'required|email|unique:users,email', 
            'password' => 'required|min:8',
            'package' => 'required', 
            'duration' => 'required|integer'
        ], [
            'subdomain.regex' => 'Pour un domaine dédié, utilisez un format valide (ex: domaine.com)',
            'subdomain.alpha_dash' => 'L\'identifiant ne doit contenir que des lettres, chiffres et tirets.'
        ]);

        try {
            $company = null;

            DB::transaction(function () use ($data, &$company) {
                $company = Company::create([
                    'name' => $data['name'], 
                    'subdomain' => strtolower($data['subdomain']),
                    'package' => $data['package'], 
                    'status' => 'active',
                    'expires_at' => now()->addMonths((int)$data['duration']),
                ]);

                User::create([
                    'name' => $data['name']." Admin", 
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']), 
                    'role' => 'client', 
                    'company_id' => $company->id
                ]);
            });

            // 2. Définition de l'URL sans protocole
            $finalUrl = $isPremium 
                ? strtolower($data['subdomain']) 
                : strtolower($data['subdomain']) . '.solutcloud.com';

            // 3. ENVOI DES MAILS SÉPARÉS
            
            // --- MAIL 1 : DESTINÉ AU CLIENT ---
            Mail::send('emails.client_access', [
                'name'      => $data['name'],
                'subdomain' => strtolower($data['subdomain']),
                'email'     => $data['email'],
                'password'  => $data['password'],
                'company'   => $company,
                'url'       => $finalUrl
            ], function($message) use ($data) {
                $message->to($data['email'])
                        ->subject('Vos accès à la plateforme SOLUTCLOUD');
            });

            // --- MAIL 2 : DESTINÉ À L'ARCHIVE (Administration) ---
            Mail::send('emails.admin_copy', [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'url'       => $finalUrl,
                'date'      => now()->format('d/m/Y H:i')
            ], function($message) {
                $message->to('sales@i-solutions.ci')
                        ->subject('📦 ARCHIVE : Nouveau client créé');
            });

            return back()->with('status', "Instance créée et emails envoyés (Client + Archive) !");

        } catch (\Exception $e) {
            Log::error("ERREUR SOLUTCLOUD : " . $e->getMessage());
            return back()->withErrors("Erreur : " . $e->getMessage())->withInput();
        }
    }

    public function suspend($id) {
        $company = Company::findOrFail($id);
        $company->update(['status' => 'suspended']);

        // LOGIQUE LWS : On calcule le nom du dossier uniquement
        // Pour un Premium : on remonte d'un niveau pour sortir de htdocs/solutcloud vers la racine
        if ($company->package === 'premium') {
            $path = "../../" . $company->subdomain . "/htdocs/.htaccess";
        } else {
            // Pour Start/Business : on est déjà au bon endroit (dans htdocs)
            $path = $company->subdomain . ".solutcloud.com/.htaccess";
        }

        try {
            Storage::disk('lws')->put($path, "Order Deny,Allow\nDeny from all");
            return back()->with('status', "L'instance {$company->subdomain} est suspendue.");
        } catch (\Exception $e) {
            return back()->with('status', "Erreur FTP : " . $e->getMessage());
        }
    }

    public function activate(Request $request, $id) {
        $company = Company::findOrFail($id);
        $duration = (int)$request->duration;
        $company->update(['status' => 'active', 'expires_at' => now()->addMonths($duration)]);

        if ($company->package === 'premium') {
            $path = "../../" . $company->subdomain . "/htdocs/.htaccess";
        } else {
            $path = $company->subdomain . ".solutcloud.com/.htaccess";
        }

        try {
            Storage::disk('lws')->delete($path);
            return back()->with('status', "L'instance {$company->subdomain} est réactivée.");
        } catch (\Exception $e) {
            return back()->with('status', "Erreur FTP lors du déverrouillage.");
        }
    }

    public function destroy(Company $company) {
        $instanceName = $company->subdomain;
        
        if ($company->package === 'premium') {
            $path = "../../" . $instanceName . "/htdocs/.htaccess";
        } else {
            $path = $instanceName . ".solutcloud.com/.htaccess";
        }

        try {
            Storage::disk('lws')->put($path, "Deny from all");
        } catch (\Exception $e) { }

        $company->delete();
        return back()->with('status', "L'instance {$instanceName} supprimée de la gestion.");
    }
}