<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendInstanceReadyEmail;
use App\Jobs\SendInstanceSetupEmails;
use App\Models\Company;
use App\Models\Demo;
use App\Models\Payment;
use App\Models\User;
use App\Models\WebsiteLead;
use App\Services\LwsInstanceStorage;
use Illuminate\Http\JsonResponse;
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
        $suspendedCount = $companies->where('status', 'suspended')->count();
        $alerts = Company::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        $availablePayments = Payment::paid()
            ->visibleInTracking()
            ->where('purpose', Payment::PURPOSE_INITIAL)
            ->whereNull('company_id')
            ->latest('paid_at')
            ->get();

        $commercialRequests = WebsiteLead::query()
            ->whereIn('type', ['order', 'quote']);
        $newCommercialRequestsQuery = (clone $commercialRequests)
            ->whereDoesntHave('payments');
        $newCommercialRequestCount = (clone $newCommercialRequestsQuery)->count();
        $newCommercialRequests = $newCommercialRequestsQuery
            ->latest()
            ->limit(4)
            ->get();
        $totalCommercialRequestCount = (clone $commercialRequests)->count();
        $orderCount = (clone $commercialRequests)->where('type', 'order')->count();
        $quoteRequestCount = (clone $commercialRequests)->where('type', 'quote')->count();

        $visiblePayments = Payment::visibleInTracking();
        $totalPaymentCount = (clone $visiblePayments)->count();
        $paidPaymentCount = (clone $visiblePayments)->paid()->count();
        $pendingPaymentCount = (clone $visiblePayments)
            ->whereIn('status', [
                Payment::STATUS_DRAFT,
                Payment::STATUS_INITIATED,
                Payment::STATUS_PENDING,
            ])
            ->count();
        $demoCount = Demo::query()->count();
        $totalDemoRequestCount = WebsiteLead::query()->trials()->count();
        $pendingDemoRequests = WebsiteLead::pendingTrialRequests();
        $pendingDemoRequestCount = $pendingDemoRequests->count();
        $latestPendingDemoRequest = $pendingDemoRequests->first();
        $pendingUpgradePayments = Payment::query()
            ->with('company')
            ->paid()
            ->where('purpose', Payment::PURPOSE_UPGRADE)
            ->whereNull('upgrade_reviewed_at')
            ->latest('paid_at')
            ->get();
        $pendingUpgradeCount = $pendingUpgradePayments->count();
        $latestPendingUpgrade = $pendingUpgradePayments->first();
        $totalUpgradeCount = Payment::query()
            ->where('purpose', Payment::PURPOSE_UPGRADE)
            ->count();
        $paidUpgradeCount = Payment::query()
            ->paid()
            ->where('purpose', Payment::PURPOSE_UPGRADE)
            ->count();
        $activityFingerprint = $this->activityFingerprint();

        $selectedPaymentId = $request->integer('payment');

        return view('admin.dashboard', compact(
            'companies',
            'totalCount',
            'pendingCount',
            'activeCount',
            'suspendedCount',
            'alerts',
            'availablePayments',
            'newCommercialRequestCount',
            'newCommercialRequests',
            'totalCommercialRequestCount',
            'orderCount',
            'quoteRequestCount',
            'totalPaymentCount',
            'paidPaymentCount',
            'pendingPaymentCount',
            'demoCount',
            'totalDemoRequestCount',
            'pendingDemoRequestCount',
            'latestPendingDemoRequest',
            'pendingUpgradeCount',
            'latestPendingUpgrade',
            'totalUpgradeCount',
            'paidUpgradeCount',
            'activityFingerprint',
            'selectedPaymentId',
        ));
    }

    public function activityStatus(): JsonResponse
    {
        return response()->json(['fingerprint' => $this->activityFingerprint()]);
    }

    private function activityFingerprint(): string
    {
        $lead = WebsiteLead::query()->latest('updated_at')->first(['id', 'updated_at']);
        $payment = Payment::query()->latest('updated_at')->first(['id', 'updated_at']);
        $demo = Demo::query()->latest('updated_at')->first(['id', 'updated_at']);

        return hash('sha256', implode('|', [
            WebsiteLead::query()->count(),
            $lead?->id,
            $lead?->updated_at?->getTimestamp(),
            Payment::query()->count(),
            $payment?->id,
            $payment?->updated_at?->getTimestamp(),
            Demo::query()->count(),
            $demo?->id,
            $demo?->updated_at?->getTimestamp(),
        ]));
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

                $customerEmail = mb_strtolower(trim($payment->customer_email));
                $user = User::query()
                    ->lockForUpdate()
                    ->where('email', $customerEmail)
                    ->first();

                if ($user !== null && (! $user->isClient() || $user->company_id !== null)) {
                    throw ValidationException::withMessages([
                        'payment_id' => 'Un compte déjà rattaché utilise cette adresse e-mail.',
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

                if ($user === null) {
                    $user = User::create([
                        'name' => $payment->customer_name,
                        'email' => $customerEmail,
                        'password' => Hash::make(Str::random(32)),
                        'role' => User::ROLE_CLIENT,
                        'company_id' => $company->id,
                    ]);
                } else {
                    $user->forceFill([
                        'name' => $payment->customer_name,
                        'company_id' => $company->id,
                    ])->save();
                }

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
            $lws->suspend($company, Company::SUSPENSION_ADMINISTRATIVE);
            $company->update([
                'status' => 'suspended',
                'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
            ]);

            return back()->with('status', 'Instance suspendue via FTP.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FTP : '.$e->getMessage());
        }
    }

    public function activate(Request $request, int $id, LwsInstanceStorage $lws)
    {
        $data = $request->validate([
            'duration' => [
                'required',
                'integer',
                'in:0,1,2,3,6,12',
            ],
        ], [
            'duration.in' => 'La durée de réactivation sélectionnée est invalide.',
            'duration.required' => 'Veuillez sélectionner une durée.',
        ]);

        $company = Company::findOrFail($id);
        $months = (int) $data['duration'];

        if ($months === 0 && ! $company->expires_at?->isFuture()) {
            throw ValidationException::withMessages([
                'duration' => 'La réactivation à 0 mois exige une échéance encore valide. Sélectionnez une prolongation.',
            ]);
        }

        try {
            $lws->reactivate($company);

            $newExpiration = match (true) {
                $months === 0 => $company->expires_at ?? now(),
                $company->expires_at?->isFuture() => $company->expires_at->copy()->addMonthsNoOverflow($months),
                default => now()->addMonthsNoOverflow($months),
            };

            $company->update([
                'status' => 'active',
                'suspension_reason' => null,
                'expires_at' => $newExpiration,
            ]);

            return back()->with(
                'status',
                $months === 0
                    ? 'Instance réactivée sans prolongation de l’échéance.'
                    : "Instance réactivée avec une prolongation de {$months} mois."
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
