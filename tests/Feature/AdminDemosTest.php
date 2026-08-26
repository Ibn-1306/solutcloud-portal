<?php

namespace Tests\Feature;

use App\Mail\DemoAccessMail;
use App\Models\Demo;
use App\Models\User;
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
        Mail::assertSent(DemoAccessMail::class, fn (DemoAccessMail $mail) => $mail->hasTo('demo@example.com'));
    }
}
