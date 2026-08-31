<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\PaymentSynchronizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_pages_share_the_new_navigation_and_subscription_content(): void
    {
        [$client] = $this->client('start');
        $this->plans('START', 5900);
        $this->plans('BUSINESS', 9900);

        $this->actingAs($client)
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Mon espace SOLUTCLOUD')
            ->assertSee('Mon entreprise')
            ->assertSee('Mon logiciel de gestion')
            ->assertSee('Mon abonnement')
            ->assertSee('client-sidebar', false)
            ->assertSee('bg-black', false)
            ->assertSee('setSidebarCollapsed(true)', false)
            ->assertSee("localStorage.getItem('solutcloud-sidebar-collapsed')", false)
            ->assertSee("sidebarCollapsed ? 'lg:pl-22' : 'lg:pl-[286px]'", false)
            ->assertSee('Ouvrir le menu SOLUTCLOUD')
            ->assertSee('Réduire le menu')
            ->assertSee('Compte')
            ->assertSee('href="'.route('client.profile').'"', false)
            ->assertSee("payload.suspension_reason === 'administrative'", false)
            ->assertSee('window.setInterval(checkAccountAccess, 4000)', false);

        $this->get(route('client.profile'))
            ->assertOk()
            ->assertSee('Informations personnelles')
            ->assertSee('Sécurité du compte')
            ->assertSee('E-mail de connexion');

        $this->get(route('client.renew'))
            ->assertOk()
            ->assertSee('Abonnement actuel : Offre START (mensuel)')
            ->assertSee('Choisissez votre période')
            ->assertSee('Passez à l’offre BUSINESS')
            ->assertSee('1 mois')
            ->assertSee('12 mois');
    }

    public function test_renewal_amount_is_calculated_on_the_server_and_redirects_to_moneroo(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'XOF');
        [$client, $company] = $this->client('start');
        $plan = $this->plan('START', 3, 17700, 30000);
        Http::fake($this->initializeResponse('renewal_checkout'));

        $this->actingAs($client)
            ->post(route('client.subscription.checkout'), [
                'action' => Payment::PURPOSE_RENEWAL,
                'plan_id' => $plan->id,
                'amount' => 1,
            ])
            ->assertRedirect('https://checkout.moneroo.io/renewal_checkout');

        $payment = Payment::where('company_id', $company->id)->latest('id')->firstOrFail();
        $this->assertSame(17700, $payment->amount);
        $this->assertSame('start', $payment->package);
        $this->assertSame(Payment::PURPOSE_RENEWAL, $payment->purpose);
        $this->assertSame(3, $payment->duration_months);

        Http::assertSent(fn ($request) => $request['amount'] === 17700
            && $request['currency'] === 'XOF'
            && $request['metadata']['purpose'] === Payment::PURPOSE_RENEWAL
            && $request['metadata']['duration_months'] === 3
        );
    }

    public function test_start_client_can_upgrade_to_business_with_the_business_price(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'XOF');
        [$client, $company] = $this->client('start');
        $plan = $this->plan('BUSINESS', 6, 59400, 108000);
        Http::fake($this->initializeResponse('upgrade_checkout'));

        $this->actingAs($client)
            ->post(route('client.subscription.checkout'), [
                'action' => Payment::PURPOSE_UPGRADE,
                'plan_id' => $plan->id,
            ])
            ->assertRedirect('https://checkout.moneroo.io/upgrade_checkout');

        $payment = Payment::where('company_id', $company->id)->latest('id')->firstOrFail();
        $this->assertSame('business', $payment->package);
        $this->assertSame(59400, $payment->amount);
        $this->assertSame(Payment::PURPOSE_UPGRADE, $payment->purpose);
    }

    public function test_non_start_client_cannot_create_an_upgrade_payment(): void
    {
        [$client] = $this->client('business');
        $businessPlan = $this->plan('BUSINESS', 1, 9900, 18000);

        $this->actingAs($client)
            ->from(route('client.renew'))
            ->post(route('client.subscription.checkout'), [
                'action' => Payment::PURPOSE_UPGRADE,
                'plan_id' => $businessPlan->id,
            ])
            ->assertRedirect(route('client.renew'))
            ->assertSessionHasErrors('action');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_paid_renewal_extends_the_current_expiry_only_once(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        [, $company] = $this->client('premium', Carbon::parse('2026-09-14 16:26:00'));
        $payment = $this->subscriptionPayment($company, [
            'package' => 'premium',
            'purpose' => Payment::PURPOSE_RENEWAL,
            'duration_months' => 3,
            'moneroo_payment_id' => 'renewal_paid',
            'status' => Payment::STATUS_INITIATED,
        ]);
        Http::fake($this->verifyResponse($payment));

        app(PaymentSynchronizer::class)->synchronize($payment);

        $company->refresh();
        $payment->refresh();
        $this->assertSame('2026-12-14 16:26:00', $company->expires_at->format('Y-m-d H:i:s'));
        $this->assertNotNull($payment->applied_at);

        app(PaymentSynchronizer::class)->synchronize($payment);

        $company->refresh();
        $this->assertSame('2026-12-14 16:26:00', $company->expires_at->format('Y-m-d H:i:s'));
    }

    public function test_paid_renewal_reactivates_a_suspended_instance_and_removes_the_ftp_lock(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        Storage::fake('lws');
        [, $company] = $this->client('start', Carbon::parse('2026-08-14 16:26:00'));
        $company->update(['status' => 'suspended']);

        $root = 'i-solutions-start.solutcloud.com';
        Storage::disk('lws')->put($root.'/index.php', '<?php');
        Storage::disk('lws')->put($root.'/main.inc.php', '<?php');
        Storage::disk('lws')->put($root.'/.htaccess', '# SOLUTCLOUD INSTANCE SUSPENDED');
        Storage::disk('lws')->put($root.'/.htaccess.solutcloud-backup', 'Original Dolibarr rules');

        $payment = $this->subscriptionPayment($company, [
            'package' => 'start',
            'purpose' => Payment::PURPOSE_RENEWAL,
            'duration_months' => 1,
            'moneroo_payment_id' => 'suspended_renewal_paid',
            'status' => Payment::STATUS_INITIATED,
        ]);
        Http::fake($this->verifyResponse($payment));

        app(PaymentSynchronizer::class)->synchronize($payment);

        $company->refresh();
        $this->assertSame('active', $company->status);
        $this->assertFalse(
            Storage::disk('lws')->exists($root.'/.htaccess.solutcloud-backup'),
            'La sauvegarde du verrou FTP doit être supprimée après la réactivation.',
        );
        $this->assertSame('Original Dolibarr rules', Storage::disk('lws')->get($root.'/.htaccess'));
    }

    public function test_paid_upgrade_changes_start_to_business(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        [, $company] = $this->client('start', Carbon::parse('2026-09-14 16:26:00'));
        $payment = $this->subscriptionPayment($company, [
            'package' => 'business',
            'purpose' => Payment::PURPOSE_UPGRADE,
            'duration_months' => 1,
            'moneroo_payment_id' => 'upgrade_paid',
            'status' => Payment::STATUS_INITIATED,
        ]);
        Http::fake($this->verifyResponse($payment));

        app(PaymentSynchronizer::class)->synchronize($payment);

        $company->refresh();
        $this->assertSame('business', $company->package);
        $this->assertSame('2026-10-14 16:26:00', $company->expires_at->format('Y-m-d H:i:s'));
    }

    /** @return array{User, Company} */
    private function client(string $package, ?Carbon $expiresAt = null): array
    {
        $company = Company::create([
            'name' => 'I-SOLUTIONS',
            'email' => 'contact@i-solutions.ci',
            'phone' => '+225 01 02 03 04 05',
            'subdomain' => 'i-solutions-'.strtolower($package),
            'custom_domain' => $package === 'premium' ? 'i-solutions.ci' : null,
            'package' => $package,
            'status' => 'active',
            'expires_at' => $expiresAt ?? now()->addMonth(),
            'subscription_started_at' => now()->subMonth(),
        ]);
        $client = User::factory()->create([
            'name' => 'Nianta Bourahima',
            'email' => strtolower($package).'@example.com',
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);

        return [$client, $company];
    }

    private function plans(string $package, int $monthlyPrice): void
    {
        foreach ([1, 2, 3, 6, 12] as $duration) {
            $this->plan($package, $duration, $monthlyPrice * $duration, $monthlyPrice * 2 * $duration);
        }
    }

    private function plan(string $package, int $duration, int $promoPrice, int $regularPrice): SubscriptionPlan
    {
        return SubscriptionPlan::create([
            'package' => $package,
            'duration_months' => $duration,
            'promo_price' => $promoPrice,
            'regular_price' => $regularPrice,
            'active' => true,
        ]);
    }

    /** @return array<string, Response> */
    private function initializeResponse(string $id): array
    {
        return [
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => $id,
                    'checkout_url' => 'https://checkout.moneroo.io/'.$id,
                ],
            ], 201),
        ];
    }

    private function subscriptionPayment(Company $company, array $attributes): Payment
    {
        return Payment::create(array_merge([
            'company_id' => $company->id,
            'customer_name' => 'Nianta Bourahima',
            'customer_email' => 'client@example.com',
            'company_name' => $company->name,
            'package' => $company->package,
            'amount' => 24900,
            'currency' => 'XOF',
            'description' => 'Gestion de l’abonnement SOLUTCLOUD',
        ], $attributes));
    }

    /** @return array<string, Response> */
    private function verifyResponse(Payment $payment): array
    {
        return [
            'https://api.moneroo.io/v1/payments/'.$payment->moneroo_payment_id.'/verify' => Http::response([
                'data' => [
                    'id' => $payment->moneroo_payment_id,
                    'status' => 'success',
                    'amount' => $payment->amount,
                    'currency' => ['code' => $payment->currency],
                    'metadata' => [
                        'payment_id' => (string) $payment->id,
                        'payment_reference' => $payment->reference,
                    ],
                ],
            ]),
        ];
    }
}
