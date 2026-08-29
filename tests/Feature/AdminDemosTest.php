<?php

namespace Tests\Feature;

use App\Mail\DemoAccessMail;
use App\Models\Demo;
use App\Models\User;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminDemosTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_identifier_is_displayed_as_read_only(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.demos.index'))
            ->assertOk()
            ->assertSee('value="demo"', false)
            ->assertSee('readonly', false)
            ->assertSee('data-phone-input', false)
            ->assertSee('https://demo.solutcloud.com');
    }

    public function test_demo_creation_always_uses_the_fixed_identifier(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.demos.store'), [
                'company_name' => 'Entreprise Démo',
                'subdomain' => 'identifiant-modifie',
                'email' => 'demo@example.com',
                'phone' => '+225 07 00 00 00 00',
                'erp_login' => 'demo.client',
                'erp_password' => 'mot-de-passe-test',
            ])
            ->assertSessionHas('status');

        $demo = Demo::firstOrFail();
        $this->assertSame(Demo::DEFAULT_SUBDOMAIN, $demo->subdomain);
        $this->assertSame('https://demo.solutcloud.com', $demo->url);
        $this->assertSame('+2250700000000', $demo->phone);
        Mail::assertSent(DemoAccessMail::class, fn (DemoAccessMail $mail) => $mail->hasTo('demo@example.com'));
    }

    public function test_pending_trial_requests_are_visible_and_can_prefill_demo_access(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $request = WebsiteLead::create([
            'type' => 'trial',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+2250102030405',
            'company_name' => 'Entreprise Alpha',
            'profile' => 'PME',
            'message' => 'Demande de test gratuit depuis solutcloud.com.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.demos.index'))
            ->assertOk()
            ->assertSee('Demandes de démonstration à traiter')
            ->assertSee($request->company_name)
            ->assertSee($request->email)
            ->assertSee('data-prepare-demo', false)
            ->assertSee('phone:set-number', false);
    }

    public function test_trial_request_is_no_longer_pending_after_demo_access_is_created(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        WebsiteLead::create([
            'type' => 'trial',
            'fullname' => 'Awa Koné',
            'email' => 'AWA@example.com',
            'phone' => '+2250102030405',
            'company_name' => 'Entreprise Alpha',
            'profile' => 'PME',
            'message' => 'Demande de test gratuit depuis solutcloud.com.',
        ]);
        Demo::create([
            'company_name' => 'Entreprise Alpha',
            'subdomain' => Demo::DEFAULT_SUBDOMAIN,
            'email' => 'awa@example.com',
            'phone' => '+2250102030405',
            'erp_login' => 'demo.alpha',
            'erp_password' => 'secret',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.demos.index'))
            ->assertOk()
            ->assertSee('0 en attente')
            ->assertDontSee('data-company="Entreprise Alpha"', false);
    }

    public function test_invalid_international_phone_is_rejected(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.demos.store'), [
                'company_name' => 'Entreprise Démo',
                'email' => 'demo@example.com',
                'phone' => '+225 12 34',
                'erp_login' => 'demo.client',
                'erp_password' => 'mot-de-passe-test',
            ])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('demos', 0);
        Mail::assertNothingSent();
    }
}
