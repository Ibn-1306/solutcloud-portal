<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\MonerooPaymentService;
use App\Services\SubscriptionPricingService;
use App\Support\OfferCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubscriptionController extends Controller
{
    public function index(Request $request, SubscriptionPricingService $pricing): View
    {
        $company = $this->company($request);
        $pendingUpgrade = Payment::query()
            ->where('company_id', $company->id)
            ->where('purpose', Payment::PURPOSE_UPGRADE)
            ->whereNull('applied_at')
            ->whereIn('status', [Payment::STATUS_INITIATED, Payment::STATUS_PENDING, Payment::STATUS_PAID])
            ->latest()
            ->first();

        return view('client.renew', [
            'company' => $company,
            'offerDetails' => OfferCatalog::details($company->package),
            'payment' => $company->payment,
            'renewalPlans' => $this->plansFor($company, $company->package, $pricing),
            'upgradePlans' => $company->package === 'start' && $pendingUpgrade === null
                ? $this->plansFor($company, 'business', $pricing)
                : collect(),
            'pendingUpgrade' => $pendingUpgrade,
            'paymentCurrency' => $pricing->currency(),
        ]);
    }

    public function checkout(
        Request $request,
        SubscriptionPricingService $pricing,
        MonerooPaymentService $moneroo,
    ): RedirectResponse {
        $data = $request->validate([
            'action' => ['required', Rule::in([Payment::PURPOSE_RENEWAL, Payment::PURPOSE_UPGRADE])],
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);
        $company = $this->company($request);
        $isUpgrade = $data['action'] === Payment::PURPOSE_UPGRADE;

        if ($isUpgrade && $company->package !== 'start') {
            throw ValidationException::withMessages([
                'action' => 'Le passage à BUSINESS est réservé aux clients START.',
            ]);
        }

        if ($isUpgrade && Payment::query()
            ->where('company_id', $company->id)
            ->where('purpose', Payment::PURPOSE_UPGRADE)
            ->whereNull('applied_at')
            ->whereIn('status', [Payment::STATUS_INITIATED, Payment::STATUS_PENDING, Payment::STATUS_PAID])
            ->exists()) {
            throw ValidationException::withMessages([
                'action' => 'Une évolution vers BUSINESS est déjà en cours de paiement ou de traitement.',
            ]);
        }

        $targetPackage = $isUpgrade ? 'business' : $company->package;
        $plan = SubscriptionPlan::query()
            ->whereKey($data['plan_id'])
            ->where('package', strtoupper($targetPackage))
            ->where('active', true)
            ->first();

        if ($plan === null) {
            throw ValidationException::withMessages([
                'plan_id' => 'La durée sélectionnée ne correspond pas à cette offre.',
            ]);
        }

        $description = $isUpgrade
            ? "Passage de SOLUTCLOUD START à BUSINESS — {$plan->duration_months} mois"
            : 'Renouvellement SOLUTCLOUD '.strtoupper($company->package)." — {$plan->duration_months} mois";

        $payment = Payment::create([
            'company_id' => $company->id,
            'customer_name' => $request->user()->name,
            'customer_email' => mb_strtolower($request->user()->email),
            'customer_phone' => $company->phone,
            'company_name' => $company->name,
            'package' => $targetPackage,
            'amount' => $pricing->amountFor($plan),
            'currency' => $pricing->currency(),
            'description' => $description,
            'purpose' => $data['action'],
            'duration_months' => $plan->duration_months,
            'status' => Payment::STATUS_DRAFT,
        ]);

        try {
            $initialized = $moneroo->initialize($payment);
            DB::transaction(function () use ($payment, $initialized): void {
                $now = now();
                $payment->checkoutAttempts()->create([
                    'moneroo_payment_id' => $initialized['id'],
                    'checkout_url' => $initialized['checkout_url'],
                    'initialized_at' => $now,
                ]);
                $payment->forceFill([
                    'moneroo_payment_id' => $initialized['id'],
                    'checkout_url' => $initialized['checkout_url'],
                    'status' => Payment::STATUS_INITIATED,
                    'initialized_at' => $now,
                    'failure_reason' => null,
                ])->save();
            });

            return redirect()->away($initialized['checkout_url']);
        } catch (Throwable $exception) {
            $payment->forceFill(['failure_reason' => $exception->getMessage()])->save();

            Log::error('CLIENT_SUBSCRIPTION_CHECKOUT_FAILED', [
                'payment_id' => $payment->id,
                'company_id' => $company->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->withErrors('Le paiement n’a pas pu être préparé. Veuillez réessayer.');
        }
    }

    private function company(Request $request): Company
    {
        $company = $request->user()->company()
            ->with('payment')
            ->first();

        abort_if($company === null, 403, 'Compte client sans entreprise.');

        return $company;
    }

    /**
     * @return Collection<int, array{id: int, duration: int, amount: int}>
     */
    private function plansFor(
        Company $company,
        string $package,
        SubscriptionPricingService $pricing,
    ): Collection {
        return SubscriptionPlan::query()
            ->where('package', strtoupper($package))
            ->where('active', true)
            ->whereIn('duration_months', [1, 2, 3, 6, 12])
            ->orderBy('duration_months')
            ->get()
            ->map(fn (SubscriptionPlan $plan): array => [
                'id' => $plan->id,
                'duration' => $plan->duration_months,
                'amount' => $pricing->amountFor($plan),
            ]);
    }
}
