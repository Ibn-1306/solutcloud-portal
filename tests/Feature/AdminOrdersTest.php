<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_orders_and_quote_requests_in_one_table(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $order = WebsiteLead::create([
            'type' => 'order',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+225 01 02 03 04 05',
            'company_name' => 'Entreprise Alpha',
            'profile' => 'PME',
            'offer' => 'START',
            'message' => 'Commande SOLUTCLOUD START.',
        ]);

        $quoteRequest = WebsiteLead::create([
            'type' => 'quote',
            'fullname' => 'Jean Kouassi',
            'email' => 'jean@example.com',
            'phone' => '+225 07 08 09 10 11',
            'company_name' => 'Entreprise Premium',
            'profile' => 'PME',
            'offer' => 'PREMIUM',
            'message' => 'Demande de devis PREMIUM.',
        ]);

        WebsiteLead::create([
            'type' => 'trial',
            'fullname' => 'Client Test',
            'email' => 'test@example.com',
            'phone' => '+225 05 00 00 00 00',
            'company_name' => 'Entreprise Test',
            'profile' => 'TPE',
            'message' => 'Demande de test.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->commercialReference())
            ->assertSee($quoteRequest->commercialReference())
            ->assertSee('Entreprise Alpha')
            ->assertSee('Entreprise Premium')
            ->assertSee('SOLUTCLOUD START')
            ->assertSee('SOLUTCLOUD PREMIUM')
            ->assertDontSee('Client Test');
    }

    public function test_client_cannot_open_the_admin_orders_module(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $this->actingAs($client)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }
}
