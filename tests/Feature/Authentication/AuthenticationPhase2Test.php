<?php

namespace Tests\Feature\Authentication;

use App\Core\Enums\RoleType;
use App\Core\Enums\UserType;
use App\Domains\Authentication\Actions\AuthenticateUser;
use App\Domains\Authentication\Actions\LockAccount;
use App\Domains\Authentication\Actions\RegisterUser;
use App\Domains\Authentication\Data\LoginData;
use App\Domains\Authentication\Data\RegisterUserData;
use App\Domains\Authentication\Exceptions\AuthenticationFailedException;
use App\Domains\User\Actions\AssignRole;
use App\Domains\User\Data\AssignRoleData;
use App\Domains\User\Policies\UserPolicy;
use App\Domains\User\Services\PermissionService;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationPhase2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_register_page_is_accessible(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_as_client(): void
    {
        $user = app(RegisterUser::class)->handle(RegisterUserData::fromArray([
            'name' => 'New Client',
            'email' => 'new-client@example.com',
            'password' => 'password123',
            'type' => UserType::Client,
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'new-client@example.com',
            'type' => UserType::Client->value,
        ]);
        $this->assertTrue($user->hasRole(RoleType::Client->value));
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_user_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'auth@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole(RoleType::Client->value);

        $authenticated = app(AuthenticateUser::class)->handle(LoginData::fromArray([
            'email' => 'auth@example.com',
            'password' => 'password123',
            'ip_address' => '127.0.0.1',
        ]));

        $this->assertTrue($authenticated->is($user));
        $this->assertAuthenticatedAs($user);
    }

    public function test_authentication_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'auth@example.com',
            'password' => 'password123',
        ]);

        $this->expectException(AuthenticationFailedException::class);

        app(AuthenticateUser::class)->handle(LoginData::fromArray([
            'email' => 'auth@example.com',
            'password' => 'wrong-password',
            'ip_address' => '127.0.0.1',
        ]));
    }

    public function test_locked_account_cannot_authenticate(): void
    {
        User::factory()->locked()->create([
            'email' => 'locked@example.com',
            'password' => 'password123',
        ]);

        $this->expectException(AuthenticationFailedException::class);

        app(AuthenticateUser::class)->handle(LoginData::fromArray([
            'email' => 'locked@example.com',
            'password' => 'password123',
            'ip_address' => '127.0.0.1',
        ]));
    }

    public function test_account_locks_after_repeated_failures(): void
    {
        $user = User::factory()->create([
            'email' => 'brute@example.com',
            'password' => 'password123',
            'failed_login_attempts' => 0,
        ]);

        $action = app(AuthenticateUser::class);

        for ($i = 0; $i < 5; $i++) {
            try {
                RateLimiter::clear(strtolower('brute@example.com').'|127.0.0.1');
                $action->handle(LoginData::fromArray([
                    'email' => 'brute@example.com',
                    'password' => 'wrong',
                    'ip_address' => '127.0.0.1',
                ]));
            } catch (AuthenticationFailedException) {
                // expected
            }
        }

        $this->assertTrue($user->fresh()->isLocked());
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = User::factory()->administrator()->create();
        $admin->assignRole(RoleType::Administrator->value);

        $this->assertTrue($admin->canAccessPanel(filament()->getPanel('admin')));
    }

    public function test_client_cannot_access_filament_panel(): void
    {
        $client = User::factory()->create();
        $client->assignRole(RoleType::Client->value);

        $this->assertFalse($client->canAccessPanel(filament()->getPanel('admin')));
    }

    public function test_roles_can_be_assigned_and_permissions_cached(): void
    {
        $user = User::factory()->create();
        $permissions = app(PermissionService::class);

        app(AssignRole::class)->handle(AssignRoleData::fromArray([
            'user_id' => $user->id,
            'roles' => [RoleType::Staff->value],
        ]));

        $user->refresh();
        $this->assertTrue($user->hasRole(RoleType::Staff->value));
        $this->assertContains('users.view', $permissions->namesFor($user));
    }

    public function test_user_policy_allows_self_update(): void
    {
        $user = User::factory()->create();
        $policy = new UserPolicy;

        $this->assertTrue($policy->update($user, $user));
    }

    public function test_user_policy_denies_foreign_update_without_permission(): void
    {
        $actor = User::factory()->create();
        $target = User::factory()->create();
        $policy = new UserPolicy;

        $this->assertFalse($policy->update($actor, $target));
    }

    public function test_lock_account_action(): void
    {
        $user = User::factory()->create();

        $locked = app(LockAccount::class)->handle($user, 'Manual lock');

        $this->assertTrue($locked->isLocked());
        $this->assertSame('Manual lock', $locked->lock_reason);
    }

    public function test_account_profile_requires_authentication(): void
    {
        $this->get(route('account.profile'))->assertRedirect(route('login'));
    }
}
