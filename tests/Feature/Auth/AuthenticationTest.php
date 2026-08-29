<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertStatus(200)
            ->assertSee('linear-gradient(180deg, #ffffff 0%, #fbfdfd 100%)', false)
            ->assertSee('border: 1px solid #d7e1e4;', false)
            ->assertDontSee('background: #050505;', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client.dashboard', absolute: false));
    }

    public function test_client_keeps_the_intended_destination_after_login(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $intended = route('subscription.expired.renew', [
            'instance' => 'djemafatis.solutcloud.com',
        ]);

        $response = $this->withSession(['url.intended' => $intended])->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect($intended);
    }

    public function test_admin_ignores_a_client_intended_destination_after_login(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->withSession([
            'url.intended' => route('client.renew'),
        ])->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionMissing('url.intended');
    }

    public function test_administratively_suspended_client_is_sent_to_the_suspension_page_after_login(): void
    {
        $company = Company::create([
            'name' => 'Entreprise suspendue',
            'email' => 'client@example.com',
            'subdomain' => 'entreprise-suspendue',
            'package' => 'start',
            'status' => 'suspended',
            'suspension_reason' => Company::SUSPENSION_ADMINISTRATIVE,
            'expires_at' => now()->addMonth(),
        ]);
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);

        $this->post('/login', [
            'email' => $client->email,
            'password' => 'password',
        ])->assertRedirect(route('account.suspended'));

        $this->assertAuthenticatedAs($client);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
