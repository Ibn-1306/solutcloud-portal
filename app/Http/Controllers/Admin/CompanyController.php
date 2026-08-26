<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendInstanceReadyEmail;
use App\Jobs\SendInstanceSetupEmails;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Services\LwsInstanceStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::latest()->get();

        $totalCount = $companies->count();
        $pendingCount = $companies->where('status', 'pending')->count();
        $activeCount = $companies->where('status', 'active')->count();
        $alerts = Company::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        $availablePayments = Payment::paid()
            ->whereNull('company_id')
            ->latest('paid_at')
            ->get();

        $selectedPaymentId = $request->integer('payment');

        return view('admin.dashboard', compact(
            'companies',
            'totalCount',
            'pendingCount',
            'activeCount',
            'alerts',
            'availablePayments',
            'selectedPaymentId',
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_id' => ['required', 'integer', 'exists:payments,id'],
            'domain' => ['required', 'string', 'max:255'],
        ]);

        try {
            [$company, $user] = DB::transaction(function () use ($data): array {
                $payment = Payment::query()->lockForUpdate()->findOrFail($data['payment_id']);

                if (! $payment->isPaid()) {
                    throw ValidationException::withMessages([
                        'payment_id' => 'L’instance ne peut être créée qu’après confirmation du paiement.',
                    ]);
                }

                if ($payment->company_id !== null) {
                    throw ValidationException::withMessages([
                        'payment_id' => 'Une instance a déjà été créée pour ce paiement.',
                    ]);
                }

                if (User::where('email', $payment->customer_email)->exists()) {
                    throw ValidationException::withMessages([
                        'payment_id' => 'Un compte client utilise déjà cette adresse e-mail.',
                    ]);
                }

                $domain = strtolower(trim($data['domain']));
                $domainRules = $payment->package === 'premium'
                    ? ['required', 'max:255', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', Rule::unique('companies', 'custom_domain')]
                    : ['required', 'max:63', 'regex:/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', Rule::unique('companies', 'subdomain')];

                validator(['domain' => $domain], ['domain' => $domainRules], [
                    'domain.regex' => 'Le domaine ou identifiant d’instance n’est pas valide.',
                ])->validate();

                $company = Company::create([
                    'name' => $payment->company_name,
                    'email' => $payment->customer_email,
                    'phone' => $payment->customer_phone,
                    'subdomain' => $payment->package === 'premium'
                        ? 'premium-'.$payment->id
                        : $domain,
                    'custom_domain' => $payment->package === 'premium' ? $domain : null,
                    'package' => $payment->package,
                    'status' => 'pending',
                    'expires_at' => now()->addYear(),
                ]);

                $user = User::create([
                    'name' => $payment->customer_name,
                    'email' => $payment->customer_email,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'client',
                    'company_id' => $company->id,
                ]);

                $payment->forceFill(['company_id' => $company->id])->save();

                return [$company, $user];
            });

            SendInstanceSetupEmails::dispatch($company->id, $user->id)
                ->onConnection('deferred');

            return back()->with('status', 'Instance placée en attente d’installation. Les deux e-mails client sont envoyés.');

        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('ERREUR CREATION CLIENT', ['message' => $exception->getMessage()]);

            return back()->withErrors('La création de l’instance a échoué. Vérifiez les journaux techniques.');
        }
    }

    public function finalizeInstance(Request $request, int $id)
    {
        $company = Company::findOrFail($id);
        $data = $request->validate([
            'erp_login' => ['required', 'string', 'max:255'],
            'erp_password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        if ($company->status !== 'pending') {
            return back()->withErrors('Cette instance n’est pas en attente d’installation.');
        }

        $company->update([
            'status' => 'active',
            'erp_login' => $data['erp_login'],
            'subscription_started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        SendInstanceReadyEmail::dispatch(
            $company->id,
            $data['erp_login'],
            $data['erp_password'],
        )->onConnection('deferred');

        return back()->with('status', 'Instance activée. Le dernier e-mail contenant les accès ERP est envoyé au client.');
    }

    public function suspend(int $id, LwsInstanceStorage $lws)
    {
        $company = Company::findOrFail($id);

        try {
            $lws->suspend($company);
            $company->update(['status' => 'suspended']);

            return back()->with('status', 'Instance suspendue via FTP.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FTP : '.$e->getMessage());
        }
    }

    public function activate(Request $request, int $id, LwsInstanceStorage $lws)
    {
        $request->validate([
            'duration' => [
                'required',
                'integer',
                'in:1,2,3,6,12',
            ],
        ], [
            'duration.in' => 'La durée de réactivation sélectionnée est invalide.',
            'duration.required' => 'Veuillez sélectionner une durée.',
        ]);

        $company = Company::findOrFail($id);

        try {
            $lws->reactivate($company);

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
                "Erreur lors de la réactivation de l'instance : ".$e->getMessage()
            );

        }
    }

    public function destroy(Company $company, LwsInstanceStorage $lws)
    {
        try {
            $lws->block($company);
        } catch (\Exception $e) {
            return back()->with('error', 'Suppression annulée : '.$e->getMessage());
        }

        $company->delete();

        return back()->with('status', 'Fiche client supprimée.');
    }
}
