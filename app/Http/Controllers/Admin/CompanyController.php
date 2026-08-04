<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail, DB, Log, Storage};

class CompanyController extends Controller 
{
    /**
     * Affiche le dashboard avec les statistiques financières réelles
     */
    public function index() 
    {
    $companies = Company::latest()->get();
    
    // Chiffre d'affaires TOTAL (Réel encaissé)
    $totalRevenue = Company::sum('total_paid');

    // MRR (Revenu mensuel récurrent estimé)
    // On change le nom de $mrr par $monthlyRevenue pour correspondre à ta vue
    $monthlyRevenue = 0; 
    foreach ($companies->where('status', 'active') as $c) {
        if ($c->package === 'start') $monthlyRevenue += 5900;
        elseif ($c->package === 'business') $monthlyRevenue += 9900;
        elseif ($c->package === 'premium') $monthlyRevenue += 24900;
    }

    $activeCount = $companies->where('status', 'active')->count();
    
    $alerts = Company::where('status', 'active')
        ->where('expires_at', '<=', now()->addDays(7))
        ->where('expires_at', '>', now())
        ->count();

    // On passe bien $monthlyRevenue ici
    return view('dashboard', compact('companies', 'activeCount', 'totalRevenue', 'monthlyRevenue', 'alerts'));
    }

    /**
     * Création d'une instance (Activation 12 mois forcée)
     */
    public function store(Request $request) 
    {
        $isPremium = $request->package === 'premium';

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|unique:companies,subdomain',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'package' => 'required',
            'premium_price' => 'nullable|numeric' 
        ]);

        // 1. Calcul des frais d'activation initiaux
        $activationFee = 0;
        if ($data['package'] === 'start') $activationFee = 59000;
        elseif ($data['package'] === 'business') $activationFee = 99000;
        elseif ($data['package'] === 'premium') $activationFee = $request->premium_price ?? 0;

        try {
            $company = null;

            // 2. Enregistrement en base de données
            DB::transaction(function () use ($data, $activationFee, &$company) {
                $company = Company::create([
                    'name' => $data['name'],
                    'subdomain' => strtolower($data['subdomain']),
                    'package' => $data['package'],
                    'status' => 'active',
                    'expires_at' => now()->addMonths(12), // Engagement 12 mois
                    'total_paid' => $activationFee,     // Premier encaissement
                ]);

                User::create([
                    'name' => $data['name']." Admin",
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'client',
                    'company_id' => $company->id
                ]);
            });

            // 3. Définition de l'URL pour les mails
            $finalUrl = $isPremium 
                ? strtolower($data['subdomain']) 
                : strtolower($data['subdomain']) . '.solutcloud.com';

            // 4. Envoi des mails (Client + Archive)
            Mail::send('emails.client_access', [
                'name' => $data['name'],
                'subdomain' => strtolower($data['subdomain']),
                'email' => $data['email'],
                'password' => $data['password'],
                'company' => $company,
                'url' => $finalUrl
            ], function($message) use ($data) {
                $message->to($data['email'])->subject('Vos accès à la plateforme SOLUTCLOUD');
            });

            Mail::send('emails.admin_copy', [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'url' => $finalUrl,
                'date' => now()->format('d/m/Y H:i')
            ], function($message) {
                $message->to('sales@i-solutions.ci')->subject('📦 ARCHIVE : Nouveau client créé');
            });

            return back()->with('status', "Instance créée et emails envoyés ! Encaissement : " . number_format($activationFee, 0, ',', ' ') . " FCFA");

        } catch (\Exception $e) {
            Log::error("ERREUR STORE SOLUTCLOUD : " . $e->getMessage());
            return back()->withErrors("Erreur : " . $e->getMessage())->withInput();
        }
    }

    /**
     * Suspension via FTP .htaccess
     */
    public function suspend($id) {
        $company = Company::findOrFail($id);
        $company->update(['status' => 'suspended']);

        $folder = ($company->package === 'premium') ? $company->subdomain : $company->subdomain . ".solutcloud.com";
        $path = "htdocs/" . $folder . "/.htaccess";

        try {
            Storage::disk('lws')->put($path, "Deny from all");
            return back()->with('status', "L'instance {$company->subdomain} a été suspendue.");
        } catch (\Exception $e) {
            return back()->with('status', "Coupé en base, mais erreur FTP : " . $e->getMessage());
        }
    }

    /**
     * Réactivation et cumul du paiement de réabonnement
     */
    public function activate(Request $request, $id) {
        $company = Company::findOrFail($id);
        $months = (int)$request->duration;
        
        $monthlyRates = ['start' => 5900, 'business' => 9900, 'premium' => 24900];
        $totalRenewal = ($monthlyRates[$company->package] ?? 0) * $months;

        $newExpiry = $company->expires_at->isPast() ? now()->addMonths($months) : $company->expires_at->addMonths($months);

        $company->update([
            'status' => 'active', 
            'expires_at' => $newExpiry,
            'total_paid' => $company->total_paid + $totalRenewal
        ]);

        $folder = ($company->package === 'premium') ? $company->subdomain : $company->subdomain . ".solutcloud.com";
        $path = "htdocs/" . $folder . "/.htaccess";

        try {
            Storage::disk('lws')->delete($path);
            return back()->with('status', "Réactivée pour {$months} mois. Encaissement ajouté : " . number_format($totalRenewal, 0, ',', ' ') . " FCFA");
        } catch (\Exception $e) {
            return back()->with('status', "Activé en base, mais le verrou FTP n'a pas pu être retiré.");
        }
    }

    /**
     * Suppression définitive
     */
    public function destroy(Company $company) {
        $instanceName = $company->subdomain;
        $folder = ($company->package === 'premium') ? $instanceName : $instanceName . ".solutcloud.com";
        $path = "htdocs/" . $folder . "/.htaccess";

        try {
            Storage::disk('lws')->put($path, "Deny from all");
        } catch (\Exception $e) { }

        $company->delete();
        return back()->with('status', "L'instance {$instanceName} supprimée de la gestion.");
    }
}