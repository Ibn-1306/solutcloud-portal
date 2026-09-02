<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_payment_stays_on_the_success_page_even_after_refresh(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');

        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'initial_paid',
        ]);

        Http::fake($this->verifyResponse($payment));
        $url = route('payments.return', ['paymentId' => $payment->moneroo_payment_id]);

        $this->get($url)
            ->assertOk()
            ->assertViewIs('payments.success')
            ->assertSee('Paiement confirmé')
            ->assertSee($payment->reference)
            ->assertSee('Vous pouvez fermer cet onglet en toute sécurité.')
            ->assertSee('img/favicon.png', false)
            ->assertDontSee('<a ', false)
            ->assertDontSee('<button', false)
            ->assertDontSee('reset-password', false);

        $payment->refresh();
        $this->assertTrue($payment->isPaid());
        $this->assertDatabaseCount('users', 0);

        $this->get($url)
            ->assertOk()
            ->assertViewIs('payments.success')
            ->assertSee('Paiement confirmé');

        Http::assertSentCount(1);
    }

    public function test_client_renewal_payment_stays_on_the_success_page(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');

        $company = Company::create([
            'name' => 'Entreprise Alpha',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'subdomain' => 'alpha',
            'package' => 'start',
            'status' => 'active',
            'expires_at' => now()->addMonth(),
            'subscription_started_at' => now()->subMonth(),
        ]);
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Awa Koné',
            'email' => 'awa@example.com',
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
            'password_initialized_at' => now(),
        ]);
        $payment = $this->payment([
            'company_id' => $company->id,
            'purpose' => Payment::PURPOSE_RENEWAL,
            'duration_months' => 3,
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'renewal_paid',
        ]);

        Http::fake($this->verifyResponse($payment));

        $this->actingAs($user)
            ->get(route('payments.return', ['paymentId' => $payment->moneroo_payment_id]))
            ->assertOk()
            ->assertViewIs('payments.success')
            ->assertSee('Votre abonnement SOLUTCLOUD a été mis à jour.')
            ->assertDontSee('<a ', false)
            ->assertDontSee('<button', false);

        $payment->refresh();
        $this->assertTrue($payment->isPaid());
        $this->assertNotNull($payment->applied_at);
    }

    public function test_paid_initial_customer_is_attached_when_admin_creates_the_instance(): void
    {
        Mail::fake();

        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create([
            'name' => 'Awa Koné',
            'email' => 'awa@example.com',
            'role' => User::ROLE_CLIENT,
            'company_id' => null,
            'password_initialized_at' => now(),
        ]);
        $payment = $this->payment([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.store'), [
                'creation_mode' => 'confirmed_payment',
                'payment_id' => $payment->id,
                'domain' => 'alpha',
            ])
            ->assertSessionHas('status');

        $company = Company::firstOrFail();

        $this->assertSame($company->id, $customer->fresh()->company_id);
        $this->assertSame($company->id, $payment->fresh()->company_id);
        $this->assertSame(2, User::count());
    }

    public function test_expired_payment_link_returns_the_same_dedicated_404_after_refresh(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.checkout_ttl_minutes', 1440);

        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'expired_payment',
            'checkout_url' => 'https://checkout.moneroo.io/expired_payment',
            'initialized_at' => now(),
        ]);
        $attempt = $payment->checkoutAttempts()->create([
            'moneroo_payment_id' => 'expired_payment',
            'checkout_url' => 'https://checkout.moneroo.io/expired_payment',
            'initialized_at' => now(),
        ]);
        Http::fake([
            'https://api.moneroo.io/v1/payments/expired_payment/verify' => Http::response([
                'data' => [
                    'id' => 'expired_payment',
                    'status' => 'expired',
                ],
            ]),
        ]);

        $url = $payment->customerCheckoutUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('/payments/checkout/'.$attempt->id, $url);
        $this->assertStringNotContainsString('checkout.moneroo.io', $url);

        $this->get($url)
            ->assertNotFound()
            ->assertHeader('Cache-Control')
            ->assertViewIs('payments.expired')
            ->assertSee('Erreur 404')
            ->assertSee('Lien de paiement expiré');

        $this->assertSame(Payment::STATUS_EXPIRED, $payment->fresh()->status);

        $this->get($url)
            ->assertNotFound()
            ->assertViewIs('payments.expired')
            ->assertSee('Lien de paiement expiré');

        $this->get(route('payments.return', ['paymentId' => 'expired_payment']))
            ->assertNotFound()
            ->assertViewIs('payments.expired');

        Http::assertSentCount(1);
    }

    public function test_expired_subscription_links_to_a_safe_renewal_entry_point(): void
    {
        $this->get(route('subscription.expired'))
            ->assertOk()
            ->assertSee('Renouveler mon abonnement')
            ->assertSee(route('login'), false)
            ->assertDontSee('Accéder à mon espace client');
    }

    private function payment(array $attributes = []): Payment
    {
        return Payment::create(array_merge([
            'customer_name' => 'Awa Koné',
            'customer_email' => 'awa@example.com',
            'customer_phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Alpha',
            'package' => 'start',
            'amount' => 70800,
            'currency' => 'XOF',
            'description' => 'Abonnement annuel SOLUTCLOUD START',
            'purpose' => Payment::PURPOSE_INITIAL,
            'status' => Payment::STATUS_DRAFT,
        ], $attributes));
    }

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
