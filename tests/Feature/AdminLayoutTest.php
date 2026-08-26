<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_modules_use_the_shared_responsive_navigation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach ([
            'admin.dashboard',
            'admin.demos.index',
            'admin.orders.index',
            'admin.payments.index',
            'admin.profile.edit',
        ] as $routeName) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('id="admin-sidebar"', false)
                ->assertSee('aria-controls="admin-sidebar"', false)
                ->assertSee('setSidebarCollapsed(true)', false)
                ->assertSee("localStorage.getItem('solutcloud-sidebar-collapsed')", false)
                ->assertSee("sidebarCollapsed ? 'lg:pl-[88px]' : 'lg:pl-[286px]'", false)
                ->assertSee('Ouvrir le menu SOLUTCLOUD')
                ->assertSee('Réduire le menu')
                ->assertSee('Tableau de bord')
                ->assertSee('Démonstrations')
                ->assertSee('Commandes')
                ->assertSee('Paiement')
                ->assertSee('Compte')
                ->assertSee('href="'.route('admin.profile.edit').'"', false)
                ->assertDontSee('Informations administrateur');
        }
    }

    public function test_dashboard_has_mobile_cards_and_desktop_instance_table(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('grid gap-4 p-4 lg:hidden', false)
            ->assertSee('hidden overflow-x-auto lg:block', false)
            ->assertSee('Centre de pilotage')
            ->assertSee('Créer une instance payée')
            ->assertSee('Instances Déployées');
    }
}
