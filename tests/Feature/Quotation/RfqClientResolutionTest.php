<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Domains\Client\Services\ClientService;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfqClientResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_or_create_from_lead_resolves_portal_user_email_to_linked_client(): void
    {
        $user = User::factory()->create([
            'email' => 'client@zytech.local',
            'email_verified_at' => now(),
        ]);

        $client = Client::query()->create([
            'user_id' => $user->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'James Mwangi',
            'email' => 'james.mwangi@example.com',
            'portal_access_granted_at' => now(),
        ]);

        $resolved = app(ClientService::class)->findOrCreateFromLead(
            'James Mwangi',
            'client@zytech.local',
            '+254712345678',
        );

        $this->assertTrue($resolved->is($client));
        $this->assertSame(1, Client::query()->count());
    }
}
