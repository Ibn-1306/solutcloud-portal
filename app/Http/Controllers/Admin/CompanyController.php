<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail, DB, Log, Storage};
use Illuminate\Support\Str;

class CompanyController extends Controller 
{
        public function index() 
        {
        $companies = Company::latest()->get();

        $totalCount = $companies->count();
        $pendingCount = $companies->where('status', 'pending')->count();
        $activeCount = $companies->where('status', 'active')->count();
        $alerts = Company::where('status', 'active')
        ->where('expires_at', '<=', now()->addDays(7))
        ->count();

    return view('admin.dashboard', compact('companies', 'totalCount', 'pendingCount', 'activeCount', 'alerts'));
    }

    public function store(Request $request) 
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|unique:companies,subdomain',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'package' => 'required|in:start,business,premium',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $company = Company::create([
                    'name' => $data['name'],
                    'email' => $data['email'], // Ajout de l'email dans la table Company
                    'phone' => $data['phone'] ?? null,
                    'subdomain' => strtolower($data['subdomain']),
                    'package' => $data['package'],
                    'status' => 'pending',
                ]);

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'client',
                    'company_id' => $company->id
                ]);
            });

            return back()->with('status', "Fiche client créée. Procédez à l'installation sur LWS.");

        } catch (\Exception $e) {
            Log::error("ERREUR CREATION CLIENT : " . $e->getMessage());
            return back()->withErrors("Erreur technique : " . $e->getMessage());
        }
    }

    public function finalizeInstance(Request $request, int $id)
    {
        $company = Company::findOrFail($id);
        $user = User::where('company_id', $company->id)->first();

        $request->validate([
            'erp_login' => 'required|string',
            'erp_password' => 'required|string',
        ]);

        $company->update([
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ]);

        $url = ($company->package === 'premium') ? "https://".$company->subdomain : "https://".$company->subdomain.".solutcloud.com";

        try {
            Mail::to($user->email)->send(new \App\Mail\InstanceReadyMail(
                $company, 
                $url, 
                $request->erp_login, 
                $request->erp_password
            ));

            return back()->with('status', "Instance activée et accès envoyés au client.");
        } catch (\Exception $e) {
            Log::error("ECHEC ENVOI MAIL ACCES : " . $e->getMessage());
            return back()->with('status', "Instance activée, mais l'envoi du mail a échoué. Vérifiez vos logs.");
        }
    }

    public function suspend(int $id) {
        $company = Company::findOrFail($id);
        $path = $this->getFtpPath($company);

        // Redirection .htaccess intelligente vers le portail
        $htaccess = "RewriteEngine On\nRewriteRule ^(.*)$ https://login.solutcloud.com/dashboard [L,R=302]";

        try {
            Storage::disk('lws')->put($path . "/.htaccess", $htaccess);
            $company->update(['status' => 'suspended']);
            return back()->with('status', "Instance suspendue via FTP.");
        } catch (\Exception $e) {
            return back()->with('error', "Erreur FTP : " . $e->getMessage());
        }
    }

    public function activate(Request $request, int $id)
    {
        $request->validate([
            'duration' => [
                'required',
                'integer',
                'in:1,2,3,6,12'
            ],
        ], [
            'duration.in' => 'La durée de réactivation sélectionnée est invalide.',
            'duration.required' => 'Veuillez sélectionner une durée.',
        ]);


        $company = Company::findOrFail($id);

        $path = $this->getFtpPath($company);


        try {

            // Suppression du verrou de suspension
            if (Storage::disk('lws')->exists($path . "/.htaccess")) {

                Storage::disk('lws')->delete($path . "/.htaccess");

            }


            $months = (int) $request->duration;


            $newExpiration = $company->expires_at && $company->expires_at->isFuture()
                ? $company->expires_at->addMonths($months)
                : now()->addMonths($months);



            $company->update([

                'status' => 'active',

                'expires_at' => $newExpiration,

            ]);



            return back()->with(
                'status',
                "Instance réactivée pour {$months} mois."
            );


        } catch (\Exception $e) {


            return back()->with(
                'error',
                "Erreur lors de la réactivation de l'instance."
            );

        }
    }

    private function getFtpPath(Company $company) {
        return ($company->package !== 'premium') 
            ? "htdocs/" . $company->subdomain . ".solutcloud.com" 
            : $company->subdomain;
    }

    public function destroy(Company $company) {
        $path = $this->getFtpPath($company);
        try {
            Storage::disk('lws')->put($path . "/.htaccess", "Deny from all");
        } catch (\Exception $e) { }

        $company->delete();
        return back()->with('status', "Fiche client supprimée.");
    }
}