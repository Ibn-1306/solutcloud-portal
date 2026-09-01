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
use App\Rules\InternationalPhoneNumber;
use App\Services\LwsInstanceStorage;
use App\Support\InternationalPhone;
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
            ->whereNull('applied_at')
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
        $currency = strtoupper((string) config('services.moneroo.currency', 'XOF'));
        $minimumAmount = $currency === 'XOF' ? 100 : 1;
        $data = $request->validate([
            'creation_mode' => ['required', Rule::in(['confirmed_payment', 'manual_payment'])],
            'payment_id' => ['nullable', 'required_if:creation_mode,confirmed_payment', 'integer', 'exists:payments,id'],
            'manual_customer_name' => ['nullable', 'required_if:creation_mode,manual_payment', 'string', 'max:255'],
            'manual_customer_email' => ['nullable', 'required_if:creation_mode,manual_payment', 'email:rfc', 'max:255'],
            'manual_customer_phone' => ['nullable', 'string', 'max:30', new InternationalPhoneNumber],
            'manual_company_name' => ['nullable', 'required_if:creation_mode,manual_payment', 'string', 'max:255'],
            'manual_package' => ['nullable', 'required_if:creation_mode,manual_payment', Rule::in(['start', 'business', 'premium'])],
            'manual_amount' => ['nullable', 'required_if:creation_mode,manual_payment', 'integer', 'min:'.$minimumAmount],
            'manual_duration_months' => ['nullable', 'required_if:creation_mode,manual_payment', 'integer', Rule::in([1, 2, 3, 6, 12])],
            'manual_payment_method' => ['nullable', 'required_if:creation_mode,manual_payment', Rule::in(['cash', 'bank_transfer', 'other'])],
            'manual_description' => ['nullable', 'string', 'max:5000'],
            'domain' => ['required', 'string', 'max:255'],
        ]);

        try {
            $adminId = $request->user()?->id;
            [$company, $user, $payment] = DB::transaction(function () use ($data, $currency, $adminId): array {
                if ($data['creation_mode'] === 'manual_payment') {
                    $payment = Payment::create([
                        'customer_name' => trim((string) $data['manual_customer_name']),
                        'customer_email' => mb_strtolower(trim((string) $data['manual_customer_email'])),
                        'customer_phone' => InternationalPhone::normalize($data['manual_customer_phone'] ?? null),
                        'company_name' => trim((string) $data['manual_company_name']),
                        'package' => $data['manual_package'],
                        'amount' => (int) $data['manual_amount'],
                        'currency' => $currency,
                        'description' => filled($data['manual_description'] ?? null)
                            ? trim((string) $data['manual_description'])
                            : null,
                        'purpose' => Payment::PURPOSE_INITIAL,
                        'duration_months' => (int) $data['manual_duration_months'],
                        'status' => Payment::STATUS_PAID,
                        'payment_channel' => $data['manual_payment_method'],
                        'verified_at' => now(),
                        'paid_at' => now(),
                        'provider_payload' => [
                            'source' => 'admin_manual_payment',
                            'recorded_by_user_id' => $adminId,
                        ],
                    ]);
                } else {
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
                }

                $customerEmail = mb_strtolower(trim($payment->customer_email));
                $user = User::query()
                    ->lockForUpdate()
                    ->where('email', $customerEmail)
                    ->first();

                if ($user !== null && (! $user->isClient() || $user->company_id !== null)) {
                    $emailField = $data['creation_mode'] === 'manual_payment'
                        ? 'manual_customer_email'
                        : 'payment_id';

                    throw ValidationException::withMessages([
                        $emailField => 'Un compte déjà rattaché utilise cette adresse e-mail.',
                    ]);
                }

                $domain = strtolower(trim($data['domain']));
                $isPremium = $payment->package === 'premium';
                $domainRules = $isPremium
                    ? ['required', 'max:255', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', Rule::unique('companies', 'custom_domain')]
                    : ['required', 'max:63', 'regex:/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', Rule::unique('companies', 'subdomain')];

                validator(['domain' => $domain], ['domain' => $domainRules], [
                    'domain.required' => $isPremium
                        ? 'Saisissez manuellement le nom de domaine dédié du client.'
                        : 'Saisissez manuellement un identifiant court pour l’instance du client.',
                    'domain.max' => $isPremium
                        ? 'Le nom de domaine dédié est trop long.'
                        : 'L’identifiant de l’instance ne peut pas dépasser 63 caractères.',
                    'domain.regex' => $isPremium
                        ? 'Saisissez uniquement le domaine, par exemple entreprise.com, sans https:// ni chemin.'
                        : 'Utilisez uniquement des lettres minuscules, chiffres et tirets, par exemple entreprise.',
                    'domain.unique' => 'Cette adresse est déjà utilisée par une autre instance.',
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
                    'expires_at' => now()->addMonthsNoOverflow($payment->duration_months ?: 12),
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

                return [$company, $user, $payment];
            });

            SendInstanceSetupEmails::dispatch($company->id, $user->id)
                ->onConnection('deferred');

            $status = $data['creation_mode'] === 'manual_payment'
                ? "Règlement manuel {$payment->reference} enregistré. L’instance est en attente d’installation et le client a été notifié."
                : 'Instance placée en attente d’installation. Un e-mail unique d’information et d’activation est envoyé au client.';

            return back()->with('status', $status);

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
        $profiles = $this->credentialProfilesFor($company->package);

        if ($company->status !== 'pending') {
            return back()->withErrors('Cette instance n’est pas en attente d’installation.');
        }

        if ($profiles === []) {
            return back()->withErrors('L’offre de cette instance ne permet pas de déterminer les accès ERP attendus.');
        }

        $profileKeys = array_keys($profiles);
        $rules = [
            'credentials' => ['required', 'array:'.implode(',', $profileKeys)],
            'credentials.*' => ['required', 'array:login,password'],
            'credentials.*.login' => ['required', 'string', 'max:255', 'distinct'],
            'credentials.*.password' => ['required', 'string', 'max:255'],
        ];
        $attributes = [];

        foreach ($profiles as $key => $label) {
            $rules["credentials.{$key}"] = ['required', 'array:login,password'];
            $rules["credentials.{$key}.login"] = ['required', 'string', 'max:255', 'distinct'];
            $rules["credentials.{$key}.password"] = ['required', 'string', 'max:255'];
            $attributes["credentials.{$key}.login"] = "{$label} — identifiant ERP";
            $attributes["credentials.{$key}.password"] = "{$label} — mot de passe ERP";
        }

        $data = $request->validate($rules, [
            'credentials.required' => 'Renseignez tous les accès ERP prévus dans cette offre.',
            'credentials.*.required' => 'Tous les comptes ERP prévus dans cette offre sont obligatoires.',
            'credentials.*.login.required' => 'Chaque compte doit posséder un identifiant ERP.',
            'credentials.*.login.distinct' => 'Chaque compte ERP doit utiliser un identifiant différent.',
            'credentials.*.password.required' => 'Chaque compte doit posséder un mot de passe ERP.',
        ], $attributes);

        $credentials = collect($profiles)
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
                'login' => trim((string) $data['credentials'][$key]['login']),
                'password' => (string) $data['credentials'][$key]['password'],
            ])
            ->values()
            ->all();

        $company->update([
            'status' => 'active',
            'erp_login' => $credentials[0]['login'],
            'subscription_started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        SendInstanceReadyEmail::dispatch(
            $company->id,
            $credentials,
        )->onConnection('deferred');

        return back()->with('status', 'Instance activée. Tous les accès ERP de l’offre ont été envoyés au client.');
    }

    /**
     * @return array<string, string>
     */
    private function credentialProfilesFor(string $package): array
    {
        return match (strtolower($package)) {
            'start' => [
                'admin' => 'Administrateur',
                'employee' => 'Employé',
            ],
            'business' => [
                'admin' => 'Administrateur',
                'employee_1' => 'Employé 1',
                'employee_2' => 'Employé 2',
                'employee_3' => 'Employé 3',
                'employee_4' => 'Employé 4',
            ],
            'premium' => [
                'super_admin' => 'Super administrateur',
            ],
            default => [],
        };
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
