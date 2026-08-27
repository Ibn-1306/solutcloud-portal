<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PaymentReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_payment_redirects_to_password_activation_then_login(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');

        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'initial_paid',
        ]);

        Http::fake($this->verifyResponse($payment));

        $response = $this->get(route('payments.return', [
            'paymentId' => $payment->moneroo_payment_id,
        ]));

        $user = User::where('email', $payment->customer_email)->firstOrFail();
        $location = (string) $response->headers->get('Location');
        $token = basename((string) parse_url($location, PHP_URL_PATH));
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        $this->assertTrue($payment->fresh()->isPaid());
        $this->assertTrue($user->isClient());
        $this->assertNull($user->company_id);
        $this->assertSame($user->email, $query['email'] ?? null);
        $this->assertSame('1', (string) ($query['activation'] ?? ''));
        $this->assertTrue(Password::broker()->tokenExists($user, $token));

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Nouveau-mot-de-passe-2026',
            'password_confirmation' => 'Nouveau-mot-de-passe-2026',
        ])->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('Nouveau-mot-de-passe-2026', $user->password));
        $this->assertNotNull($user->password_initialized_at);
    }

    public function test_client_renewal_payment_redirects_to_account_dashboard(): void
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
            ->assertRedirect(route('client.dashboard'))
            ->assertSessionHas('status');

        $this->assertTrue($payment->fresh()->isPaid());
        $this->assertNotNull($payment->fresh()->applied_at);
    }

    public function test_paid_initial_customer_is_attached_when_admin_creates_the_instance(): void
    {
        Mail::fake();

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
                'payment_id' => $payment->id,
                'domain' => 'alpha',
            ])
            ->assertSessionHas('status');

        $company = Company::firstOrFail();

        $this->assertSame($company->id, $customer->fresh()->company_id);
        $this->assertSame($company->id, $payment->fresh()->company_id);
        $this->assertSame(2, User::count());
    }

    public function test_expired_subscription_links_directly_to_renewal_management(): void
    {
        $this->get(route('subscription.expired'))
            ->assertOk()
            ->assertSee('Renouveler mon abonnement')
            ->assertSee(route('client.renew'), false)
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
