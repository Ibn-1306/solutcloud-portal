<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Demo;
use App\Models\Payment;
use App\Models\User;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_modules_use_the_shared_responsive_navigation(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach ([
            'admin.dashboard',
            'admin.demos.index',
            'admin.orders.index',
            'admin.payments.index',
            'admin.client-security.index',
            'admin.profile.edit',
        ] as $routeName) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('id="admin-sidebar"', false)
                ->assertSee('aria-controls="admin-sidebar"', false)
                ->assertSee('setSidebarCollapsed(true)', false)
                ->assertSee("localStorage.getItem('solutcloud-sidebar-collapsed')", false)
                ->assertSee("sidebarCollapsed ? 'lg:pl-22' : 'lg:pl-[286px]'", false)
                ->assertSee('Ouvrir le menu SOLUTCLOUD')
                ->assertSee('Réduire le menu')
                ->assertSee('Tableau de bord')
                ->assertSee('Démonstrations')
                ->assertSee('Commandes')
                ->assertSee('Paiement')
                ->assertSee('Sécurité clients')
                ->assertSee('Compte')
                ->assertSee('href="'.route('admin.profile.edit').'"', false)
                ->assertDontSee('Informations administrateur');
        }
    }

    public function test_dashboard_has_mobile_cards_and_desktop_instance_table(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('grid gap-4 p-4 lg:hidden', false)
            ->assertSee('hidden overflow-x-auto lg:block', false)
            ->assertSee('mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2', false)
            ->assertSee('hover:border-blue-300', false)
            ->assertSee('<article class="rounded-3xl border border-amber-300', false)
            ->assertSee('<article class="rounded-3xl border border-violet-300', false)
            ->assertSee('admin-data-table', false)
            ->assertSee('Centre de pilotage')
            ->assertSee('Voir paiement')
            ->assertDontSee('Nouveau paiement')
            ->assertSee('Créer une instance')
            ->assertSee('Instances Déployées');
    }

    public function test_dashboard_summarizes_modules_and_highlights_actionable_activity(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $lead = WebsiteLead::create([
            'type' => 'order',
            'fullname' => 'Awa Koné',
            'email' => 'awa@example.com',
            'phone' => '+2250102030405',
            'company_name' => 'Entreprise Alpha',
            'profile' => 'PME',
            'offer' => 'START',
            'message' => 'Prévoir la reprise des données.',
        ]);
        $demoRequest = WebsiteLead::create([
            'type' => 'trial',
            'fullname' => 'Mariam Traoré',
            'email' => 'mariam@example.com',
            'phone' => '+2250708091011',
            'company_name' => 'Entreprise Delta',
            'profile' => 'PME',
            'message' => 'Je souhaite tester SOLUTCLOUD.',
        ]);
        $payment = Payment::create([
            'customer_name' => 'Jean Kouassi',
            'customer_email' => 'jean@example.com',
            'company_name' => 'Entreprise Beta',
            'package' => 'business',
            'amount' => 118800,
            'currency' => 'XOF',
            'description' => 'Déploiement prioritaire.',
            'purpose' => Payment::PURPOSE_INITIAL,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);
        $pendingCompany = Company::create([
            'name' => 'Entreprise Gamma',
            'email' => 'gamma@example.com',
            'phone' => '+2250701020304',
            'subdomain' => 'entreprise-gamma',
            'package' => 'start',
            'status' => 'pending',
            'expires_at' => now()->addYear(),
        ]);
        Demo::create([
            'company_name' => 'Entreprise Démo',
            'subdomain' => 'demo',
            'email' => 'demo@example.com',
            'phone' => '+2250501020304',
            'erp_login' => 'demo.user',
            'erp_password' => 'demo-password',
            'starts_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Alertes opérationnelles')
            ->assertSee('Priorités à traiter')
            ->assertSee('4 action(s) requise(s)')
            ->assertSee('Nouvelle demande à traiter')
            ->assertSee($lead->commercialReference())
            ->assertSee('Accès client à préparer')
            ->assertSee($payment->reference)
            ->assertSee('Instance à finaliser')
            ->assertSee('data-finalize-alert="'.$pendingCompany->id.'"', false)
            ->assertSee('id="instance-'.$pendingCompany->id.'"', false)
            ->assertSee('data-company-package="start"', false)
            ->assertSee('name="credentials[admin][login]"', false)
            ->assertSee('name="credentials[employee][login]"', false)
            ->assertSee('name="credentials[employee_4][login]"', false)
            ->assertSee('name="credentials[super_admin][login]"', false)
            ->assertSee('target.scrollIntoView', false)
            ->assertSee('openFinalize(button)', false)
            ->assertSee('Demande de démo à traiter')
            ->assertSee($demoRequest->company_name)
            ->assertSee('Voir les demandes')
            ->assertSee('dashboard-action-card--active', false)
            ->assertSee('dashboard-action-arrow', false)
            ->assertDontSee('dashboard-action-arrow--always', false)
            ->assertSee('Voir les paiements')
            ->assertSee('Ma vue générale')
            ->assertDontSee('Vue générale de l’activité')
            ->assertDontSee('Tous vos modules')
            ->assertSee('Démonstrations')
            ->assertSee('Accès créés')
            ->assertSee('Instances à créer')
            ->assertDontSee('Instances actives')
            ->assertSee('window.setInterval(checkDashboardActivity, 5000)', false)
            ->assertSee('href="'.route('admin.dashboard', ['payment' => $payment->id]).'#new-instance"', false)
            ->assertSee('href="'.route('admin.orders.index').'"', false)
            ->assertSee('href="'.route('admin.payments.index').'"', false)
            ->assertSee('href="'.route('admin.demos.index').'"', false);
    }

    public function test_dashboard_alerts_paid_business_upgrades_and_refreshes_when_activity_changes(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $company = Company::create([
            'name' => 'Entreprise Upgrade',
            'email' => 'upgrade@example.com',
            'subdomain' => 'entreprise-upgrade',
            'package' => 'start',
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ]);
        $upgrade = Payment::create([
            'company_id' => $company->id,
            'customer_name' => 'Client Upgrade',
            'customer_email' => 'upgrade@example.com',
            'company_name' => $company->name,
            'package' => 'business',
            'amount' => 59400,
            'currency' => 'XOF',
            'description' => 'Passage START vers BUSINESS',
            'purpose' => Payment::PURPOSE_UPGRADE,
            'duration_months' => 6,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Passages à BUSINESS')
            ->assertSee('Évolution BUSINESS à finaliser')
            ->assertSee($upgrade->reference)
            ->assertSee('Le compte reste sur START jusqu’à votre validation.')
            ->assertSee('Finaliser l’évolution')
            ->assertSee('href="'.route('admin.payments.index').'"', false)
            ->assertSee('Confirmés')
            ->assertSee('À traiter');

        $this->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee('Statut évolution')
            ->assertSee('À finaliser')
            ->assertSee('Offre actuelle : START')
            ->assertSee('Finaliser l’évolution')
            ->assertSee('action="'.route('admin.payments.finalize-upgrade', $upgrade).'"', false);

        $firstFingerprint = $this->getJson(route('admin.dashboard.activity-status'))
            ->assertOk()
            ->json('fingerprint');

        WebsiteLead::create([
            'type' => 'quote',
            'fullname' => 'Nouveau prospect',
            'email' => 'nouveau@example.com',
            'company_name' => 'Nouvelle entreprise',
            'offer' => 'PREMIUM',
        ]);

        $secondFingerprint = $this->getJson(route('admin.dashboard.activity-status'))
            ->assertOk()
            ->json('fingerprint');
        $this->assertNotSame($firstFingerprint, $secondFingerprint);

        $this->post(route('admin.payments.finalize-upgrade', $upgrade))
            ->assertSessionHas('status');
        $this->assertSame('business', $company->fresh()->package);
        $this->assertNotNull($upgrade->fresh()->applied_at);
        $this->assertNotNull($upgrade->fresh()->upgrade_reviewed_at);

        $this->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee('Évolution finalisée')
            ->assertSee('BUSINESS actif')
            ->assertDontSee('Finaliser l’évolution');
    }
}
