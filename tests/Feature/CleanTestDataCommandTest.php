<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanTestDataCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_refuses_a_non_mysql_connection_without_deleting_data(): void
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

        $this->artisan('solutcloud:clean-test-data', [
            '--admin-email' => $admin->email,
            '--confirm-database' => 'base-test',
        ])
            ->expectsOutputToContain('Nettoyage refusé : connexion inattendue sqlite/')
            ->assertFailed();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }
}
