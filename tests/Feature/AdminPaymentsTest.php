<?php

namespace Tests\Feature;

use App\Mail\AccountInvitationMail;
use App\Mail\InstallationPendingMail;
use App\Mail\InstanceReadyMail;
use App\Mail\PaymentLinkMail;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_payment_tracking_and_instance_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee('Créer un paiement')
            ->assertSee('Tableau de suivi')
            ->assertSee('Commande ou demande associée')
            ->assertDontSee('(facultatif)');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee("En attente d'installation")
            ->assertSee('Créer une instance payée')
            ->assertSee('Instances Déployées')
            ->assertSee('Ouvrir Paiement')
            ->assertDontSee('Ouvrir Paiements')
            ->assertDontSee('Chemin FTP');
    }

    public function test_manual_payment_mode_exposes_a_complete_field_reset(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee('Création manuelle')
            ->assertSee('Sélectionner une offre')
            ->assertSee('Description / Notes additionnelles')
            ->assertSee('const clearManualFields = () =>', false)
            ->assertSee("fields[key].value = '';", false);
    }

    public function test_admin_can_remove_an_unpaid_payment_from_tracking_without_deleting_its_audit_record(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company();
        $payment = $this->payment([
            'company_id' => $company->id,
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'payment_to_archive',
            'checkout_url' => 'https://checkout.moneroo.io/payment_to_archive',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee($payment->reference)
            ->assertSee('Supprimer')
            ->assertSee('Le lien Moneroo distant ne sera pas annulé.');

        $this->delete(route('admin.payments.destroy', $payment))
            ->assertSessionHas('status');

        $payment->refresh();
        $this->assertNotNull($payment->archived_at);
        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('companies', ['id' => $company->id]);

        $this->get(route('admin.payments.index'))->assertOk();

        $this->get(route('admin.payments.index'))
            ->assertOk()
            ->assertDontSee($payment->reference);
    }

    public function test_admin_cannot_remove_a_paid_payment_from_tracking(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payment = $this->payment([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.payments.destroy', $payment))
            ->assertSessionHasErrors();

        $this->assertNull($payment->fresh()->archived_at);
    }

    public function test_admin_can_create_and_email_a_moneroo_payment_link(): void
    {
        Mail::fake();
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'XOF');

        Http::fake([
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => 'pay_test_123',
                    'checkout_url' => 'https://checkout.moneroo.io/pay_test_123',
                ],
            ], 201),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'customer_name' => 'Awa Koné',
                'customer_email' => 'AWA@example.com',
                'customer_phone' => '+225 01 02 03 04 05',
                'company_name' => 'Entreprise Alpha',
                'package' => 'start',
                'amount' => 70800,
                'description' => 'Abonnement annuel SOLUTCLOUD START',
            ])
            ->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('status');

        $payment = Payment::firstOrFail();

        $this->assertSame(
            sprintf('PAY-%s-%04d', now()->format('y'), $payment->id),
            $payment->reference,
        );
        $this->assertSame(Payment::STATUS_INITIATED, $payment->status);
        $this->assertSame('awa@example.com', $payment->customer_email);
        $this->assertSame('pay_test_123', $payment->moneroo_payment_id);
        $this->assertNotNull($payment->link_sent_at);

        Mail::assertSent(PaymentLinkMail::class, fn (PaymentLinkMail $mail) => $mail->hasTo('awa@example.com') && $mail->payment->is($payment)
        );

        Http::assertSent(fn ($request) => $request->url() === 'https://api.moneroo.io/v1/payments/initialize'
            && $request['amount'] === 70800
            && $request['currency'] === 'XOF'
            && $request['metadata']['payment_id'] === (string) $payment->id
            && $request['metadata']['payment_reference'] === $payment->reference
            && $request['metadata']['purpose'] === Payment::PURPOSE_INITIAL
            && ! array_key_exists('duration_months', $request['metadata'])
            && ! array_key_exists('company_id', $request['metadata'])
        );
    }

    public function test_sandbox_can_use_the_predefined_usd_payment_gateway(): void
    {
        Mail::fake();
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'USD');

        Http::fake([
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => 'pay_test_usd',
                    'checkout_url' => 'https://checkout.moneroo.io/pay_test_usd',
                ],
            ], 201),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'customer_name' => 'Awa Koné',
                'customer_email' => 'awa@example.com',
                'company_name' => 'Entreprise Sandbox',
                'package' => 'start',
                'amount' => 10,
                'description' => 'Test Sandbox SOLUTCLOUD START',
            ])
            ->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('status');

        $payment = Payment::firstOrFail();
        $this->assertSame('USD', $payment->currency);
        $this->assertSame(10, $payment->amount);

        Http::assertSent(fn ($request) => $request['currency'] === 'USD' && $request['amount'] === 10
        );
    }

    public function test_signed_webhook_verifies_the_payment_before_marking_it_paid(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.webhook_secret', 'webhook-secret');

        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'pay_test_paid',
            'checkout_url' => 'https://checkout.moneroo.io/pay_test_paid',
        ]);

        Http::fake([
            'https://api.moneroo.io/v1/payments/pay_test_paid/verify' => Http::response([
                'data' => [
                    'id' => 'pay_test_paid',
                    'status' => 'success',
                    'amount' => 70800,
                    'currency' => ['code' => 'XOF'],
                    'metadata' => [
                        'payment_id' => (string) $payment->id,
                        'payment_reference' => $payment->reference,
                    ],
                ],
            ]),
        ]);

        $payload = json_encode([
            'event' => 'payment.success',
            'data' => ['id' => 'pay_test_paid'],
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $payload, 'webhook-secret');

        $this->call('POST', route('webhooks.moneroo'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_MONEROO_SIGNATURE' => $signature,
        ], $payload)
            ->assertOk()
            ->assertJson(['status' => 'received']);

        $payment->refresh();
        $this->assertTrue($payment->isPaid());
        $this->assertNotNull($payment->verified_at);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_webhook_rejects_an_invalid_signature(): void
    {
        config()->set('services.moneroo.webhook_secret', 'webhook-secret');

        $this->withHeader('X-Moneroo-Signature', 'invalid')
            ->postJson(route('webhooks.moneroo'), [
                'event' => 'payment.success',
                'data' => ['id' => 'pay_unknown'],
            ])
            ->assertForbidden();
    }

    public function test_instance_can_only_be_created_from_an_unlinked_paid_payment(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $unpaid = $this->payment();

        $this->actingAs($admin)
            ->post(route('admin.companies.store'), [
                'payment_id' => $unpaid->id,
                'domain' => 'alpha',
            ])
            ->assertSessionHasErrors('payment_id');

        $this->assertDatabaseCount('companies', 0);

        $unpaid->update([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.store'), [
                'payment_id' => $unpaid->id,
                'domain' => 'alpha',
            ])
            ->assertSessionHas('status');

        $company = Company::firstOrFail();
        $client = User::where('role', User::ROLE_CLIENT)->firstOrFail();

        $this->assertSame('pending', $company->status);
        $this->assertSame('alpha', $company->subdomain);
        $this->assertSame('alpha.solutcloud.com', $company->resolved_ftp_path);
        $this->assertSame($company->id, $unpaid->fresh()->company_id);
        $this->assertSame($company->id, $client->company_id);

        Mail::assertSent(InstallationPendingMail::class, fn ($mail) => $mail->hasTo($client->email));
        Mail::assertSent(AccountInvitationMail::class, fn ($mail) => $mail->hasTo($client->email)
            && $mail->company->is($company)
            && $mail->payment?->is($unpaid)
        );
    }

    public function test_finalization_activates_the_instance_and_sends_erp_access(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payment = $this->payment([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.companies.store'), [
            'payment_id' => $payment->id,
            'domain' => 'alpha',
        ]);

        Mail::fake();
        $company = Company::firstOrFail();
        $client = User::where('role', User::ROLE_CLIENT)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.companies.finalize', $company->id), [
                'erp_login' => 'admin.alpha',
                'erp_password' => 'Secret-ERP-2026',
            ])
            ->assertSessionHas('status');

        $company->refresh();
        $this->assertSame('active', $company->status);
        $this->assertSame('admin.alpha', $company->erp_login);
        $this->assertNotNull($company->subscription_started_at);
        $this->assertNull($company->erp_password);

        Mail::assertSent(InstanceReadyMail::class, fn (InstanceReadyMail $mail) => $mail->hasTo($client->email)
            && $mail->login === 'admin.alpha'
            && $mail->password === 'Secret-ERP-2026'
        );
    }

    public function test_lws_folders_are_resolved_automatically_for_each_offer(): void
    {
        $start = new Company(['package' => 'start', 'subdomain' => 'alpha']);
        $business = new Company(['package' => 'business', 'subdomain' => 'beta']);
        $premium = new Company([
            'package' => 'premium',
            'subdomain' => 'premium-99',
            'custom_domain' => 'Entreprise.COM',
        ]);

        $this->assertSame('alpha.solutcloud.com', $start->resolved_ftp_path);
        $this->assertSame('beta.solutcloud.com', $business->resolved_ftp_path);
        $this->assertSame('entreprise.com', $premium->resolved_ftp_path);
        $this->assertSame([
            'alpha.solutcloud.com',
            'htdocs/alpha.solutcloud.com',
        ], $start->ftpPathCandidates());
        $this->assertSame([
            'entreprise.com',
            'htdocs/entreprise.com',
        ], $premium->ftpPathCandidates());
    }

    public function test_expired_subscription_page_is_public_and_exposes_reactivation_actions(): void
    {
        $this->get(route('subscription.expired'))
            ->assertOk()
            ->assertSee('Votre abonnement est arrivé à expiration.')
            ->assertSee('Renouveler mon abonnement')
            ->assertSee(route('client.renew'), false)
            ->assertSee('sales@i-solutions.ci')
            ->assertSee('+225 01 01 55 95 05')
            ->assertSee('tel:+2250101559505', false);
    }

    public function test_expired_page_tracks_subdomains_and_dedicated_domains_until_reactivation(): void
    {
        $start = $this->company([
            'subdomain' => 'djemafatis',
            'package' => 'start',
            'status' => 'suspended',
        ]);

        $this->get(route('subscription.expired', ['instance' => $start->instance_url]))
            ->assertOk()
            ->assertSee('Cette page vérifie automatiquement la réactivation')
            ->assertSee('window.location.replace', false)
            ->assertSee('djemafatis.solutcloud.com', false);

        $this->getJson(route('subscription.expired.status', ['instance' => $start->instance_url]))
            ->assertOk()
            ->assertHeader('Cache-Control')
            ->assertJson([
                'status' => 'suspended',
                'redirect_url' => null,
            ]);

        $start->update(['status' => 'active']);

        $this->getJson(route('subscription.expired.status', ['instance' => 'djemafatis.solutcloud.com']))
            ->assertOk()
            ->assertJson([
                'status' => 'active',
                'redirect_url' => 'https://djemafatis.solutcloud.com',
            ]);

        $premium = $this->company([
            'subdomain' => 'premium-client',
            'custom_domain' => 'gestion-client.ci',
            'package' => 'premium',
            'status' => 'active',
        ]);

        $this->getJson(route('subscription.expired.status', ['instance' => 'https://gestion-client.ci/espace']))
            ->assertOk()
            ->assertJson([
                'status' => 'active',
                'redirect_url' => $premium->instance_url,
            ]);
    }

    public function test_admin_suspends_and_reactivates_a_start_instance_at_the_ftp_root(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('alpha.solutcloud.com/index.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/main.inc.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/.htaccess', 'Original Dolibarr rules');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company([
            'subdomain' => 'alpha',
            'package' => 'start',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.suspend', $company->id))
            ->assertSessionHas('status');

        Storage::disk('lws')->assertExists('alpha.solutcloud.com/.htaccess');
        Storage::disk('lws')->assertExists('alpha.solutcloud.com/.htaccess.solutcloud-backup');
        $this->assertStringContainsString(
            'https://login.solutcloud.com/abonnement-expire',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );
        $this->assertStringContainsString(
            'instance=https%3A%2F%2Falpha.solutcloud.com',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );
        $this->assertStringContainsString(
            'Cache-Control "no-store, no-cache, must-revalidate, max-age=0"',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );
        $this->assertSame('suspended', $company->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.activate', $company->id), ['duration' => 1])
            ->assertSessionHas('status');

        Storage::disk('lws')->assertExists('alpha.solutcloud.com/.htaccess');
        Storage::disk('lws')->assertMissing('alpha.solutcloud.com/.htaccess.solutcloud-backup');
        $this->assertSame(
            'Original Dolibarr rules',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );
        $this->assertSame('active', $company->fresh()->status);
    }

    public function test_start_instance_falls_back_to_htdocs_only_when_it_contains_dolibarr(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('alpha.solutcloud.com/unrelated.txt', 'wrong folder');
        Storage::disk('lws')->put('htdocs/alpha.solutcloud.com/index.php', '<?php');
        Storage::disk('lws')->put('htdocs/alpha.solutcloud.com/main.inc.php', '<?php');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company(['subdomain' => 'alpha', 'package' => 'business']);

        $this->actingAs($admin)
            ->post(route('admin.suspend', $company->id))
            ->assertSessionHas('status');

        Storage::disk('lws')->assertMissing('alpha.solutcloud.com/.htaccess');
        Storage::disk('lws')->assertExists('htdocs/alpha.solutcloud.com/.htaccess');
        $this->assertSame('suspended', $company->fresh()->status);
    }

    public function test_status_is_not_changed_when_no_dolibarr_root_can_be_found(): void
    {
        Storage::fake('lws');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company(['subdomain' => 'missing']);

        $this->actingAs($admin)
            ->post(route('admin.suspend', $company->id))
            ->assertSessionHas('error');

        $this->assertSame('active', $company->fresh()->status);
        Storage::disk('lws')->assertMissing('missing.solutcloud.com/.htaccess');
        Storage::disk('lws')->assertMissing('htdocs/missing.solutcloud.com/.htaccess');
    }

    public function test_admin_suspends_a_premium_instance_at_the_lws_root(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('entreprise.com/index.php', '<?php');
        Storage::disk('lws')->put('entreprise.com/main.inc.php', '<?php');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company([
            'subdomain' => 'premium-99',
            'custom_domain' => 'entreprise.com',
            'package' => 'premium',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.suspend', $company->id))
            ->assertSessionHas('status');

        Storage::disk('lws')->assertExists('entreprise.com/.htaccess');
        $this->assertSame('suspended', $company->fresh()->status);
    }

    private function company(array $attributes = []): Company
    {
        return Company::create(array_merge([
            'name' => 'Entreprise Alpha',
            'email' => 'direction@example.com',
            'phone' => '+225 01 02 03 04 05',
            'subdomain' => 'alpha',
            'package' => 'start',
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ], $attributes));
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
            'status' => Payment::STATUS_DRAFT,
        ], $attributes));
    }
}
