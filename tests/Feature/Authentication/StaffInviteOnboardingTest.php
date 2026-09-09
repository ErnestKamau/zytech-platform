<?php

namespace Tests\Feature\Authentication;

use App\Core\Enums\RoleType;
use App\Core\Enums\UserType;
use App\Domains\Authentication\Services\StaffInviteService;
use App\Domains\Communication\Mail\StaffInviteMail;
use App\Filament\Pages\AdminOnboarding;
use App\Filament\Pages\Auth\Login as AdminLogin;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class StaffInviteOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_staff_invite_email_is_sent_and_accept_prefills_admin_login(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'type' => UserType::Staff,
            'email' => 'new-staff@zytech.test',
            'email_verified_at' => null,
        ]);
        $user->assignRole(RoleType::Staff->value);

        $token = app(StaffInviteService::class)->send($user);

        Mail::assertSent(StaffInviteMail::class, function (StaffInviteMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && str_contains($mail->inviteUrl, '/admin/invite/');
        });

        $this->assertNotNull($user->fresh()->admin_invite_sent_at);
        $this->assertNull($user->fresh()->admin_onboarded_at);

        $this->get(route('admin.invite.accept', ['token' => $token]))
            ->assertRedirect('/admin/login');

        $this->assertSame('new-staff@zytech.test', session('admin_invite_email'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->admin_invite_token);
    }

    public function test_admin_login_forces_remember_and_routes_to_onboarding(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Staff,
            'email' => 'onboard@zytech.test',
            'password' => 'password123',
            'admin_onboarded_at' => null,
        ]);
        $user->assignRole(RoleType::Staff->value);

        Livewire::test(AdminLogin::class)
            ->fillForm([
                'email' => 'onboard@zytech.test',
                'password' => 'password123',
                'remember' => true,
            ])
            ->call('authenticate')
            ->assertRedirect(AdminOnboarding::getUrl());

        $this->assertAuthenticatedAs($user);
    }

    public function test_onboarding_completion_marks_user_and_can_go_to_website(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Staff,
            'admin_onboarded_at' => null,
        ]);
        $user->assignRole(RoleType::Staff->value);

        $this->actingAs($user);

        Livewire::test(AdminOnboarding::class)
            ->call('nextStep')
            ->assertSet('step', 2)
            ->call('finish', 'website')
            ->assertRedirect(url('/'));

        $this->assertNotNull($user->fresh()->admin_onboarded_at);
    }

    public function test_invalid_invite_token_redirects_with_error(): void
    {
        $this->get(route('admin.invite.accept', ['token' => 'not-a-real-token']))
            ->assertRedirect('/admin/login');
    }
}
