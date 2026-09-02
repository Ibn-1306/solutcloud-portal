<?php

namespace Tests\Feature;

use App\Mail\AccountInvitationMail;
use App\Mail\ClientPasswordResetMail;
use App\Mail\InstanceInstallationMail;
use App\Mail\InstanceReadyMail;
use App\Mail\PaymentLinkMail;
use App\Models\ClientSecurityLink;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Models\WebsiteLead;
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
            ->assertDontSee('Montant encaissé')
            ->assertDontSee('(facultatif)');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee("En attente d'installation")
            ->assertSee('Créer une instance')
            ->assertSee('Instances Déployées')
            ->assertDontSee('Ouvrir Paiement')
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
            ->assertSee('Précisions du client / Notes additionnelles')
            ->assertSee('Éléments compris dans l’offre')
            ->assertSee('Pack START, CRM, projets, temps, marketing et enquêtes')
            ->assertSee('Serveur dédié et isolé')
            ->assertSee('data-phone-input', false)
            ->assertSee('phone:set-number', false)
            ->assertSee('const clearManualFields = () =>', false)
            ->assertSee("fields[key].value = '';", false);
    }

    public function test_commercial_request_uses_only_the_clients_notes_as_payment_description(): void
    {
        Mail::fake();
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'XOF');

        Http::fake([
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => 'pay_client_notes',
                    'checkout_url' => 'https://checkout.moneroo.io/pay_client_notes',
                ],
            ], 201),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $lead = WebsiteLead::create([
            'type' => 'order',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+2250102030405',
            'company_name' => 'Entreprise Alpha',
            'profile' => 'PME',
            'offer' => 'START',
            'message' => "Commande de l’offre SOLUTCLOUD START.\n\nPrécisions :\nImporter les données existantes.\nPrévoir une formation comptable.",
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'website_lead_id' => $lead->id,
                'customer_name' => $lead->fullname,
                'customer_email' => $lead->email,
                'customer_phone' => $lead->phone,
                'company_name' => $lead->company_name,
                'package' => 'start',
                'amount' => 70800,
                'description' => 'Règlement '.$lead->commercialReference().' — SOLUTCLOUD START',
            ])
            ->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('status');

        $payment = Payment::firstOrFail();

        $this->assertSame("Importer les données existantes.\nPrévoir une formation comptable.", $payment->description);
        $this->assertStringNotContainsString('Commande de l’offre', $payment->description);
        $this->assertStringNotContainsString('Précisions :', $payment->description);
        $this->assertStringNotContainsString($lead->commercialReference(), $payment->description);
    }

    public function test_payment_link_can_be_created_without_client_notes(): void
    {
        Mail::fake();
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.currency', 'XOF');

        Http::fake([
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => 'pay_without_notes',
                    'checkout_url' => 'https://checkout.moneroo.io/pay_without_notes',
                ],
            ], 201),
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'customer_name' => 'Awa Koné',
                'customer_email' => 'awa@example.com',
                'company_name' => 'Entreprise Alpha',
                'package' => 'business',
                'amount' => 118800,
            ])
            ->assertRedirect(route('admin.payments.index'))
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $payment = Payment::firstOrFail();
        $this->assertNull($payment->description);

        Http::assertSent(fn ($request) => $request->url() === 'https://api.moneroo.io/v1/payments/initialize'
            && $request['description'] === "Règlement {$payment->reference} — SOLUTCLOUD BUSINESS"
        );
    }

    public function test_manual_payment_rejects_an_invalid_international_phone(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'customer_name' => 'Awa Koné',
                'customer_email' => 'awa@example.com',
                'customer_phone' => '+225 12 34',
                'company_name' => 'Entreprise Alpha',
                'package' => 'start',
                'amount' => 70800,
                'description' => 'Abonnement annuel SOLUTCLOUD START',
            ])
            ->assertSessionHasErrors('customer_phone');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_initial_payment_rejects_an_email_already_used_by_a_client_account(): void
    {
        Http::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'email' => 'client@example.com',
            'company_id' => $this->company([
                'email' => 'client@example.com',
                'subdomain' => 'client-existant',
            ])->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'customer_name' => 'Client Existant',
                'customer_email' => ' CLIENT@EXAMPLE.COM ',
                'company_name' => 'Entreprise Existante',
                'package' => 'start',
                'amount' => 5900,
            ])
            ->assertSessionHasErrors('customer_email');

        $this->assertDatabaseCount('payments', 0);
        Http::assertNothingSent();
    }

    public function test_manual_instance_creation_rejects_an_existing_email_without_partial_records(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'email' => 'client@example.com',
            'company_id' => $this->company([
                'email' => 'client@example.com',
                'subdomain' => 'client-manuel',
            ])->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.store'), [
                'creation_mode' => 'manual_payment',
                'manual_customer_name' => 'Client Existant',
                'manual_customer_email' => 'CLIENT@example.com',
                'manual_company_name' => 'Entreprise Existante',
                'manual_package' => 'business',
                'manual_amount' => 9900,
                'manual_duration_months' => 1,
                'manual_payment_method' => 'cash',
                'domain' => 'entreprise-existante',
            ])
            ->assertSessionHasErrors('manual_customer_email');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('companies', 1);
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
        $this->assertSame('+2250102030405', $payment->customer_phone);
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
                'creation_mode' => 'confirmed_payment',
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
                'creation_mode' => 'confirmed_payment',
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

        Mail::assertSent(InstanceInstallationMail::class, fn ($mail) => $mail->hasTo($client->email)
            && $mail->company->is($company)
            && $mail->payment?->is($unpaid)
            && filled($mail->activationUrl)
        );
        Mail::assertSentCount(1);
    }

    public function test_admin_can_create_a_cash_paid_instance_without_moneroo(): void
    {
        Mail::fake();
        Http::fake();
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Paiement manuel — espèces, virement ou autre')
            ->assertSee('Aucun lien Moneroo ne sera généré')
            ->assertSee('name="manual_payment_method"', false)
            ->assertSee('data-phone-input', false);

        $this->post(route('admin.companies.store'), [
            'creation_mode' => 'manual_payment',
            'manual_customer_name' => 'Awa Koné',
            'manual_customer_email' => 'awa.cash@example.com',
            'manual_customer_phone' => '+2250102030405',
            'manual_company_name' => 'Entreprise Espèces',
            'manual_package' => 'business',
            'manual_amount' => 59400,
            'manual_duration_months' => 6,
            'manual_payment_method' => 'cash',
            'manual_description' => 'Reçu de caisse RC-2026-42',
            'domain' => 'entreprise-especes',
        ])
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $payment = Payment::firstOrFail();
        $company = Company::firstOrFail();
        $client = User::where('role', User::ROLE_CLIENT)->firstOrFail();

        $this->assertTrue($payment->isPaid());
        $this->assertSame('cash', $payment->payment_channel);
        $this->assertSame('Espèces', $payment->channelLabel());
        $this->assertSame(6, $payment->duration_months);
        $this->assertNull($payment->moneroo_payment_id);
        $this->assertNull($payment->checkout_url);
        $this->assertSame($company->id, $payment->company_id);
        $this->assertSame('business', $company->package);
        $this->assertSame('pending', $company->status);
        $this->assertSame('https://entreprise-especes.solutcloud.com', $company->instance_url);
        $this->assertSame($company->id, $client->company_id);

        Http::assertNothingSent();
        Mail::assertSent(InstanceInstallationMail::class, fn (InstanceInstallationMail $mail): bool => $mail->hasTo($client->email)
            && $mail->payment?->is($payment)
        );

        $this->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee($payment->reference)
            ->assertSee('Espèces');
    }

    public function test_admin_manually_defines_short_urls_independently_of_the_company_name(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $startPayment = $this->payment([
            'company_name' => 'Société Internationale de Distribution et de Transformation de Produits Agricoles',
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['payment' => $startPayment->id]))
            ->assertOk()
            ->assertSee('Saisie manuelle obligatoire')
            ->assertSee('Informations transmises par le client')
            ->assertSee($startPayment->company_name)
            ->assertDontSee('const slugify', false)
            ->assertSee('fillPayment(true)', false)
            ->assertSee("if (clearDomain) inputDomain.value = '';", false);

        $this->post(route('admin.companies.store'), [
            'creation_mode' => 'confirmed_payment',
            'payment_id' => $startPayment->id,
            'domain' => 'siditra',
        ])->assertSessionHas('status');

        $startCompany = Company::where('package', 'start')->firstOrFail();
        $this->assertSame($startPayment->company_name, $startCompany->name);
        $this->assertSame('siditra', $startCompany->subdomain);
        $this->assertNull($startCompany->custom_domain);
        $this->assertSame('https://siditra.solutcloud.com', $startCompany->instance_url);
        $this->assertSame('siditra.solutcloud.com', $startCompany->resolved_ftp_path);

        $premiumPayment = $this->payment([
            'customer_email' => 'premium@example.com',
            'company_name' => 'Cabinet International de Conseil et d’Expertise Financière',
            'package' => 'premium',
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->post(route('admin.companies.store'), [
            'creation_mode' => 'confirmed_payment',
            'payment_id' => $premiumPayment->id,
            'domain' => 'cabinet-expert.ci',
        ])->assertSessionHas('status');

        $premiumCompany = Company::where('package', 'premium')->firstOrFail();
        $this->assertSame('cabinet-expert.ci', $premiumCompany->custom_domain);
        $this->assertSame('https://cabinet-expert.ci', $premiumCompany->instance_url);
        $this->assertSame('cabinet-expert.ci', $premiumCompany->resolved_ftp_path);
    }

    public function test_finalization_activates_the_instance_and_sends_all_start_erp_accesses(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payment = $this->payment([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.companies.store'), [
            'creation_mode' => 'confirmed_payment',
            'payment_id' => $payment->id,
            'domain' => 'alpha',
        ]);

        Mail::fake();
        $company = Company::firstOrFail();
        $client = User::where('role', User::ROLE_CLIENT)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.companies.finalize', $company->id), [
                'credentials' => [
                    'admin' => ['login' => 'admin.alpha', 'password' => 'A1'],
                    'employee' => ['login' => 'employe.alpha', 'password' => 'E1'],
                ],
            ])
            ->assertSessionHas('status');

        $company->refresh();
        $this->assertSame('active', $company->status);
        $this->assertSame('admin.alpha', $company->erp_login);
        $this->assertNotNull($company->subscription_started_at);
        $this->assertNull($company->erp_password);

        Mail::assertSent(InstanceReadyMail::class, function (InstanceReadyMail $mail) use ($client): bool {
            return $mail->hasTo($client->email)
                && $mail->credentials === [
                    ['key' => 'admin', 'label' => 'Administrateur', 'login' => 'admin.alpha', 'password' => 'A1'],
                    ['key' => 'employee', 'label' => 'Employé', 'login' => 'employe.alpha', 'password' => 'E1'],
                ];
        });
    }

    public function test_finalization_requires_the_exact_credentials_for_business_and_premium_offers(): void
    {
        Mail::fake();
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $business = $this->company([
            'name' => 'Entreprise Business',
            'email' => 'business@example.com',
            'subdomain' => 'business',
            'package' => 'business',
            'status' => 'pending',
        ]);
        User::factory()->create([
            'company_id' => $business->id,
            'role' => User::ROLE_CLIENT,
            'email' => $business->email,
        ]);

        $incompleteBusinessCredentials = [
            'admin' => ['login' => 'admin.business', 'password' => 'Secret-Admin-2026'],
            'employee_1' => ['login' => 'employe1.business', 'password' => 'Secret-Employe1-2026'],
            'employee_2' => ['login' => 'employe2.business', 'password' => 'Secret-Employe2-2026'],
            'employee_3' => ['login' => 'employe3.business', 'password' => 'Secret-Employe3-2026'],
        ];

        $this->actingAs($admin)
            ->post(route('admin.companies.finalize', $business->id), ['credentials' => $incompleteBusinessCredentials])
            ->assertSessionHasErrors('credentials.employee_4');

        $this->assertSame('pending', $business->fresh()->status);

        $businessCredentials = $incompleteBusinessCredentials + [
            'employee_4' => ['login' => 'employe4.business', 'password' => 'Secret-Employe4-2026'],
        ];

        $this->actingAs($admin)
            ->post(route('admin.companies.finalize', $business->id), ['credentials' => $businessCredentials])
            ->assertSessionHas('status');

        $this->assertSame('active', $business->fresh()->status);
        Mail::assertSent(InstanceReadyMail::class, fn (InstanceReadyMail $mail): bool => $mail->company->is($business)
            && count($mail->credentials) === 5
            && $mail->credentials[4]['label'] === 'Employé 4');

        $premium = $this->company([
            'name' => 'Entreprise Premium',
            'email' => 'premium-access@example.com',
            'subdomain' => 'premium-access',
            'package' => 'premium',
            'status' => 'pending',
        ]);
        User::factory()->create([
            'company_id' => $premium->id,
            'role' => User::ROLE_CLIENT,
            'email' => $premium->email,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.finalize', $premium->id), [
                'credentials' => [
                    'super_admin' => ['login' => 'superadmin.premium', 'password' => 'Secret-Premium-2026'],
                ],
            ])
            ->assertSessionHas('status');

        $this->assertSame('superadmin.premium', $premium->fresh()->erp_login);
        Mail::assertSent(InstanceReadyMail::class, fn (InstanceReadyMail $mail): bool => $mail->company->is($premium)
            && $mail->credentials === [
                ['key' => 'super_admin', 'label' => 'Super administrateur', 'login' => 'superadmin.premium', 'password' => 'Secret-Premium-2026'],
            ]);
    }

    public function test_lws_folders_are_resolved_automatically_for_each_offer(): void
    {
        $start = new Company(['package' => 'start', 'subdomain' => 'ALPHA']);
        $business = new Company(['package' => 'business', 'subdomain' => 'beta']);
        $premium = new Company([
            'package' => 'premium',
            'subdomain' => 'premium-99',
            'custom_domain' => 'Entreprise.COM',
        ]);

        $this->assertSame('alpha.solutcloud.com', $start->resolved_ftp_path);
        $this->assertSame('alpha', $start->subdomain);
        $this->assertSame('https://alpha.solutcloud.com', $start->instance_url);
        $this->assertSame('beta.solutcloud.com', $business->resolved_ftp_path);
        $this->assertSame('entreprise.com', $premium->resolved_ftp_path);
        $this->assertSame('entreprise.com', $premium->custom_domain);
        $this->assertSame('https://entreprise.com', $premium->instance_url);
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
            ->assertSee(route('login'), false)
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
            ->assertSee('djemafatis.solutcloud.com', false)
            ->assertSee(route('subscription.expired.renew', [
                'instance' => 'djemafatis.solutcloud.com',
            ]), false);

        $this->get(route('subscription.expired', [
            'instance' => 'httpsAFFdjemafatis.solutcloud.com',
        ]))
            ->assertOk()
            ->assertSee('Cette page vérifie automatiquement la réactivation')
            ->assertSee('djemafatis.solutcloud.com', false);

        $this->getJson(route('subscription.expired.status', ['instance' => $start->instance_url]))
            ->assertOk()
            ->assertHeader('Cache-Control')
            ->assertJson([
                'status' => 'suspended',
                'redirect_url' => null,
            ]);

        $start->update(['status' => 'active']);

        $this->getJson(route('subscription.expired.status', [
            'instance' => 'httpsAFFdjemafatis.solutcloud.com',
        ]))
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

    public function test_expired_subscription_renewal_uses_the_matching_client_session(): void
    {
        $company = $this->company([
            'subdomain' => 'djemafatis',
            'status' => 'suspended',
        ]);
        $renewUrl = route('subscription.expired.renew', [
            'instance' => 'djemafatis.solutcloud.com',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get($renewUrl)
            ->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', $renewUrl);
        $this->assertGuest();

        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);

        $this->actingAs($client)
            ->get($renewUrl)
            ->assertRedirect(route('client.renew'));
    }

    public function test_admin_must_suspend_an_instance_before_deleting_it(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('alpha.solutcloud.com/index.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/main.inc.php', '<?php');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company(['status' => 'active']);

        $this->actingAs($admin)
            ->delete(route('companies.destroy', $company))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
        Storage::disk('lws')->assertMissing('alpha.solutcloud.com/.htaccess');
    }

    public function test_admin_can_delete_a_suspended_instance_and_its_client_account(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('alpha.solutcloud.com/index.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/main.inc.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/.htaccess', '# SOLUTCLOUD INSTANCE SUSPENDED');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company([
            'status' => 'suspended',
            'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
        ]);
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
            'email' => 'client-delete@example.com',
        ]);
        $payment = $this->payment(['company_id' => $company->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Supprimer le client '.$company->name, false)
            ->assertSee(route('companies.destroy', $company), false);

        $this->delete(route('companies.destroy', $company))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
        $this->assertDatabaseMissing('users', ['id' => $client->id]);
        $this->assertNull($payment->fresh()->company_id);
        $this->assertStringContainsString(
            'https://login.solutcloud.com/compte-supprime?instance=alpha.solutcloud.com',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );

        $this->get(route('account.deleted', ['instance' => 'alpha.solutcloud.com']))
            ->assertOk()
            ->assertHeader('Cache-Control')
            ->assertSee('Ce compte a été supprimé.')
            ->assertSee('L’administrateur SOLUTCLOUD a supprimé ce compte');
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
            'https://login.solutcloud.com/compte-suspendu',
            Storage::disk('lws')->get('alpha.solutcloud.com/.htaccess'),
        );
        $this->assertStringContainsString(
            'instance=alpha.solutcloud.com',
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

    public function test_administratively_suspended_client_is_blocked_from_every_account_page(): void
    {
        $company = $this->company([
            'status' => 'suspended',
            'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
        ]);
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);

        $this->actingAs($client)
            ->get(route('client.dashboard'))
            ->assertRedirect(route('account.suspended'));

        $this->get(route('client.renew'))
            ->assertRedirect(route('account.suspended'));

        $this->get(route('profile.edit'))
            ->assertRedirect(route('account.suspended'));

        $this->get(route('account.suspended'))
            ->assertOk()
            ->assertHeader('Cache-Control')
            ->assertSee('Votre espace client est')
            ->assertSee('suspendu.')
            ->assertSee($company->name)
            ->assertSee('Contacter le service client')
            ->assertDontSee('class="topline"', false)
            ->assertSee('box-shadow:none', false)
            ->assertDontSee('Renouveler mon abonnement');
    }

    public function test_suspension_page_refreshes_to_the_account_after_admin_reactivation(): void
    {
        $company = $this->company([
            'status' => 'suspended',
            'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
        ]);
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);

        $this->actingAs($client)
            ->getJson(route('account.suspended.status'))
            ->assertOk()
            ->assertJson(['status' => 'suspended', 'redirect_url' => null]);

        $company->update(['status' => 'active', 'suspension_reason' => null]);

        $this->getJson(route('account.suspended.status'))
            ->assertOk()
            ->assertJson([
                'status' => 'active',
                'redirect_url' => route('client.dashboard'),
            ]);
    }

    public function test_admin_can_reactivate_without_extending_or_creating_a_payment(): void
    {
        Storage::fake('lws');
        Storage::disk('lws')->put('alpha.solutcloud.com/index.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/main.inc.php', '<?php');
        Storage::disk('lws')->put('alpha.solutcloud.com/.htaccess', '# SOLUTCLOUD INSTANCE SUSPENDED');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $expiration = now()->addMonths(4)->startOfSecond();
        $company = $this->company([
            'status' => 'suspended',
            'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
            'expires_at' => $expiration,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.activate', $company->id), ['duration' => 0])
            ->assertSessionHas('status', 'Instance réactivée sans prolongation de l’échéance.');

        $company->refresh();
        $this->assertSame('active', $company->status);
        $this->assertNull($company->suspension_reason);
        $this->assertTrue($expiration->equalTo($company->expires_at));
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_a_commercial_request_can_only_create_one_payment_record(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $lead = WebsiteLead::create([
            'type' => 'order',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'company_name' => 'Entreprise Alpha',
            'offer' => 'START',
            'message' => 'Reprise comptable.',
        ]);
        $this->payment(['website_lead_id' => $lead->id]);

        $this->actingAs($admin)
            ->get(route('admin.payments.index'))
            ->assertOk()
            ->assertDontSee('<option value="'.$lead->id.'"', false);

        $this->post(route('admin.payments.store'), [
            'website_lead_id' => $lead->id,
            'customer_name' => $lead->fullname,
            'customer_email' => $lead->email,
            'company_name' => $lead->company_name,
            'package' => 'start',
            'amount' => 70800,
        ])->assertSessionHasErrors('website_lead_id');

        $this->assertSame(1, $lead->payments()->count());
    }

    public function test_admin_can_regenerate_an_unpaid_link_but_never_a_paid_one(): void
    {
        Mail::fake();
        config()->set('services.moneroo.secret', 'test-secret');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'old_link',
            'checkout_url' => 'https://checkout.moneroo.io/old_link',
        ]);
        $payment->checkoutAttempts()->create([
            'moneroo_payment_id' => 'old_link',
            'checkout_url' => 'https://checkout.moneroo.io/old_link',
            'initialized_at' => now()->subHour(),
        ]);
        Http::fake([
            'https://api.moneroo.io/v1/payments/initialize' => Http::response([
                'data' => [
                    'id' => 'new_link',
                    'checkout_url' => 'https://checkout.moneroo.io/new_link',
                ],
            ], 201),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.initialize', $payment))
            ->assertSessionHas('status');

        $payment->refresh();
        $this->assertSame('new_link', $payment->moneroo_payment_id);
        $this->assertSame(2, $payment->checkoutAttempts()->count());
        $this->assertNotNull($payment->checkoutAttempts()->where('moneroo_payment_id', 'old_link')->firstOrFail()->superseded_at);
        Mail::assertSent(PaymentLinkMail::class);

        $payment->update(['status' => Payment::STATUS_PAID, 'paid_at' => now()]);
        Http::fake();
        $this->post(route('admin.payments.initialize', $payment->fresh()))
            ->assertSessionHasErrors();
        Http::assertNothingSent();
    }

    public function test_admin_security_module_sends_activation_then_password_reset_and_tracks_both(): void
    {
        Mail::fake();
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = $this->company();
        /** @var User $client */
        $client = User::factory()->create([
            'name' => 'Awa Koné',
            'email' => 'client@example.com',
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
            'password_initialized_at' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.client-security.send'), ['user_id' => $client->id])
            ->assertSessionHas('status');
        Mail::assertSent(AccountInvitationMail::class, fn ($mail) => $mail->hasTo($client->email));
        $this->assertDatabaseHas('client_security_links', [
            'user_id' => $client->id,
            'requested_by' => $admin->id,
            'type' => ClientSecurityLink::TYPE_ACTIVATION,
            'status' => ClientSecurityLink::STATUS_SENT,
        ]);

        Mail::fake();
        $client->update(['password_initialized_at' => now()]);
        $this->post(route('admin.client-security.send'), ['user_id' => $client->id])
            ->assertSessionHas('status');
        Mail::assertSent(ClientPasswordResetMail::class, fn ($mail) => $mail->hasTo($client->email));
        $this->assertDatabaseHas('client_security_links', [
            'user_id' => $client->id,
            'type' => ClientSecurityLink::TYPE_RESET,
            'status' => ClientSecurityLink::STATUS_SENT,
        ]);

        $this->get(route('admin.client-security.index'))
            ->assertOk()
            ->assertSee('Sécurité clients')
            ->assertSee('Tableau de suivi')
            ->assertSee('Activation initiale')
            ->assertSee('Mot de passe oublié')
            ->assertSee('Aucun mot de passe client n’est visible ni communiqué.')
            ->assertDontSee($client->password);

        $this->get(route('admin.payments.index'))
            ->assertOk()
            ->assertDontSee('Sécurité de l’espace client');
    }

    public function test_a_confirmed_superseded_moneroo_link_is_still_recognized(): void
    {
        config()->set('services.moneroo.secret', 'test-secret');
        config()->set('services.moneroo.webhook_secret', 'webhook-secret');
        $payment = $this->payment([
            'status' => Payment::STATUS_INITIATED,
            'moneroo_payment_id' => 'new_link',
            'checkout_url' => 'https://checkout.moneroo.io/new_link',
        ]);
        $payment->checkoutAttempts()->create([
            'moneroo_payment_id' => 'old_link',
            'checkout_url' => 'https://checkout.moneroo.io/old_link',
            'initialized_at' => now()->subHour(),
            'superseded_at' => now(),
        ]);
        Http::fake([
            'https://api.moneroo.io/v1/payments/old_link/verify' => Http::response([
                'data' => [
                    'id' => 'old_link',
                    'status' => 'success',
                    'amount' => $payment->amount,
                    'currency' => ['code' => $payment->currency],
                    'metadata' => [
                        'payment_id' => (string) $payment->id,
                        'payment_reference' => $payment->reference,
                    ],
                ],
            ]),
        ]);

        $payload = json_encode([
            'event' => 'payment.success',
            'data' => ['id' => 'old_link'],
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $payload, 'webhook-secret');

        $this->call('POST', route('webhooks.moneroo'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_MONEROO_SIGNATURE' => $signature,
        ], $payload)->assertOk()->assertJson(['status' => 'received']);

        $this->assertTrue($payment->fresh()->isPaid());
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
