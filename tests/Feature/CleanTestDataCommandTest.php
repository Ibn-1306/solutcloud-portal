<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\WebsiteLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CleanTestDataCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_keeps_only_admin_plans_and_migrations(): void
    {
        $admin = User::factory()->create([
            'email' => 'sales@i-solutions.ci',
            'role' => User::ROLE_ADMIN,
        ]);
        $company = Company::create([
            'name' => 'Entreprise Test',
            'email' => 'client@example.com',
            'subdomain' => 'entreprise-test',
            'package' => 'start',
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);
        User::factory()->create([
            'email' => 'client@example.com',
            'role' => User::ROLE_CLIENT,
            'company_id' => $company->id,
        ]);
        $lead = WebsiteLead::create([
            'type' => 'order',
            'fullname' => 'Client Test',
            'email' => 'client@example.com',
            'offer' => 'START',
        ]);
        Payment::create([
            'website_lead_id' => $lead->id,
            'company_id' => $company->id,
            'customer_name' => 'Client Test',
            'customer_email' => 'client@example.com',
            'company_name' => 'Entreprise Test',
            'package' => 'start',
            'amount' => 10,
            'currency' => 'USD',
            'description' => 'Paiement de test',
            'status' => Payment::STATUS_DRAFT,
        ]);
        SubscriptionPlan::create([
            'package' => 'START',
            'duration_months' => 1,
            'promo_price' => 5900,
            'regular_price' => 10000,
            'active' => true,
        ]);

        $database = (string) config('database.connections.mysql.database');

        $this->artisan('solutcloud:clean-test-data', [
            '--admin-email' => $admin->email,
            '--confirm-database' => $database,
        ])->assertSuccessful();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => $admin->email,
            'role' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseCount('companies', 0);
        $this->assertDatabaseCount('website_leads', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('subscription_plans', 1);
        $this->assertGreaterThan(0, DB::table('migrations')->count());
    }
}
