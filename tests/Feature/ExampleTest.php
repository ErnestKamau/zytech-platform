<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_health_endpoint_is_ok(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_the_home_page_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_the_admin_login_page_is_accessible(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_users_use_uuid_primary_keys(): void
    {
        if (! $this->databaseIsAvailable()) {
            $this->markTestSkipped('Database is not configured yet. Create PostgreSQL and run migrations to enable this test.');
        }

        $this->artisan('migrate', ['--force' => true])->assertSuccessful();

        $user = User::factory()->create();

        $this->assertIsString($user->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $user->id
        );
    }

    private function databaseIsAvailable(): bool
    {
        try {
            Schema::connection()->getConnection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
