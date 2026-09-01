<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_password_reset_request_is_disabled(): void
    {
        $user = User::factory()->create();

        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => $user->email])->assertNotFound();
    }

    public function test_account_activation_screen_can_be_rendered_with_an_issued_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
            'activation' => 1,
        ]))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('name="activation" value="1"', false);
    }

    public function test_password_can_be_initialized_with_a_valid_token(): void
    {
        $user = User::factory()->create(['password_initialized_at' => null]);
        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'activation' => 1,
            'password' => 'Ab1',
            'password_confirmation' => 'Ab1',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $user->refresh();

        $this->assertTrue(Hash::check('Ab1', $user->password));
        $this->assertNotNull($user->password_initialized_at);
    }
}
