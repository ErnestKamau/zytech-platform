<?php

namespace Database\Seeders;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\ClientType;
use App\Core\Enums\CommunicationMethod;
use App\Core\Enums\DocumentVisibility;
use App\Core\Enums\PreferredContactMethod;
use App\Domains\Client\Services\ClientAnalyticsService;
use App\Domains\Client\Services\ClientService;
use App\Domains\Client\Services\CommunicationService;
use App\Domains\Client\Services\DocumentService;
use App\Domains\Client\Services\TimelineService;
use App\Models\Client;
use App\Models\ClientGroup;
use App\Models\ClientTag;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $tags = $this->seedTags();
        $groups = $this->seedGroups();
        $sales = User::query()->where('email', 'admin@zytech.local')->first();

        foreach ($this->catalogue() as $row) {
            $client = Client::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'type' => $row['type'],
                    'status' => $row['status'],
                    'name' => $row['name'],
                    'legal_name' => $row['legal_name'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'industry' => $row['industry'] ?? null,
                    'website' => $row['website'] ?? null,
                    'preferred_contact_method' => $row['preferred_contact_method'] ?? PreferredContactMethod::Email,
                    'summary' => $row['summary'] ?? null,
                    'assigned_sales_id' => $sales?->id,
                ],
            );

            if (! $client->preferences()->exists()) {
                app(ClientService::class)->initializeRecord($client);
            }

            $client->contacts()->updateOrCreate(
                ['email' => $row['email'], 'client_id' => $client->id],
                [
                    'name' => $row['contact_name'] ?? $row['name'],
                    'role' => $row['contact_role'] ?? 'Primary contact',
                    'phone' => $row['phone'] ?? null,
                    'is_primary' => true,
                    'sort_order' => 0,
                ],
            );

            if (isset($row['address'])) {
                $client->addresses()->updateOrCreate(
                    ['line1' => $row['address']['line1'], 'client_id' => $client->id],
                    [
                        ...$row['address'],
                        'is_primary' => true,
                        'sort_order' => 0,
                    ],
                );
            }

            if (isset($row['tags'])) {
                $client->tags()->sync(collect($row['tags'])->map(fn (string $slug): string => $tags[$slug]->id)->all());
            }

            if (isset($row['groups'])) {
                $client->groups()->sync(collect($row['groups'])->map(fn (string $slug): string => $groups[$slug]->id)->all());
            }

            if (isset($row['projects'])) {
                $projectIds = Project::query()->whereIn('slug', $row['projects'])->pluck('id');
                $client->projects()->syncWithoutDetaching(
                    $projectIds->mapWithKeys(fn (string $id): array => [$id => ['is_favorite' => true]])->all(),
                );
            }

            foreach ($row['documents'] ?? [] as $document) {
                if ($client->documents()->where('title', $document['title'])->exists()) {
                    continue;
                }

                app(DocumentService::class)->register($client, [
                    ...$document,
                    'visibility' => DocumentVisibility::Staff,
                ]);
            }

            foreach ($row['communications'] ?? [] as $communication) {
                if ($client->communications()->where('subject', $communication['subject'])->exists()) {
                    continue;
                }

                app(CommunicationService::class)->log($client, $communication);
            }

            if (isset($row['timeline_only'])) {
                foreach ($row['timeline_only'] as $event) {
                    app(TimelineService::class)->record(
                        $client,
                        ClientTimelineEvent::from($event['type']),
                        $event['title'],
                        $event['description'] ?? null,
                    );
                }
            }
        }

        app(ClientAnalyticsService::class)->forget();
    }

    /**
     * @return array<string, ClientTag>
     */
    private function seedTags(): array
    {
        $rows = [
            ['name' => 'Residential', 'slug' => 'residential'],
            ['name' => 'Commercial', 'slug' => 'commercial'],
            ['name' => 'Referral', 'slug' => 'referral'],
        ];

        $tags = [];

        foreach ($rows as $row) {
            $tags[$row['slug']] = ClientTag::query()->updateOrCreate(['slug' => $row['slug']], $row);
        }

        return $tags;
    }

    /**
     * @return array<string, ClientGroup>
     */
    private function seedGroups(): array
    {
        $rows = [
            ['name' => 'Nairobi metro', 'slug' => 'nairobi-metro', 'description' => 'Clients in Nairobi and environs.'],
            ['name' => 'Coastal', 'slug' => 'coastal', 'description' => 'Mombasa, Kilifi, and coastal counties.'],
        ];

        $groups = [];

        foreach ($rows as $row) {
            $groups[$row['slug']] = ClientGroup::query()->updateOrCreate(['slug' => $row['slug']], $row);
        }

        return $groups;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalogue(): array
    {
        return [
            [
                'name' => 'James Mwangi',
                'email' => 'james.mwangi@example.com',
                'phone' => '+254712345678',
                'type' => ClientType::Individual,
                'status' => ClientStatus::Active,
                'preferred_contact_method' => PreferredContactMethod::Phone,
                'summary' => 'Repeat residential client — driveway and landscaping in Karen.',
                'contact_name' => 'James Mwangi',
                'contact_role' => 'Homeowner',
                'address' => [
                    'label' => 'Home',
                    'line1' => 'Karen Road, Karen',
                    'city' => 'Nairobi',
                    'county' => 'Nairobi',
                ],
                'tags' => ['residential'],
                'groups' => ['nairobi-metro'],
                'projects' => ['courtyard-house'],
                'documents' => [
                    ['title' => 'Signed contract — Karen driveway', 'kind' => 'contract', 'stored_path' => 'clients/sample/contract-karen.pdf'],
                ],
                'communications' => [
                    [
                        'channel' => CommunicationMethod::Phone,
                        'subject' => 'Initial site visit',
                        'summary' => 'Discussed paver layout and drainage requirements.',
                        'occurred_at' => now()->subDays(14),
                    ],
                ],
            ],
            [
                'name' => 'Sunrise Estates Ltd',
                'legal_name' => 'Sunrise Estates Limited',
                'email' => 'projects@sunrise-estates.co.ke',
                'phone' => '+254733445566',
                'type' => ClientType::Company,
                'status' => ClientStatus::Prospect,
                'industry' => 'Property development',
                'website' => 'https://sunrise-estates.example',
                'preferred_contact_method' => PreferredContactMethod::Email,
                'summary' => 'Commercial developer evaluating hardscape packages for a new phase.',
                'contact_name' => 'Grace Wanjiku',
                'contact_role' => 'Project coordinator',
                'address' => [
                    'label' => 'Head office',
                    'line1' => 'Westlands Business Park',
                    'city' => 'Nairobi',
                    'county' => 'Nairobi',
                ],
                'tags' => ['commercial', 'referral'],
                'groups' => ['nairobi-metro'],
                'communications' => [
                    [
                        'channel' => CommunicationMethod::Email,
                        'subject' => 'BOQ request',
                        'summary' => 'Requested itemised quote for communal areas and access roads.',
                        'occurred_at' => now()->subDays(3),
                    ],
                ],
                'timeline_only' => [
                    [
                        'type' => ClientTimelineEvent::QuotationRequested->value,
                        'title' => 'Quotation requested',
                        'description' => 'Awaiting formal submission via website form.',
                    ],
                ],
            ],
            [
                'name' => 'Amina Hassan',
                'email' => 'amina.hassan@example.com',
                'phone' => '+254722998877',
                'type' => ClientType::Individual,
                'status' => ClientStatus::Prospect,
                'preferred_contact_method' => PreferredContactMethod::WhatsApp,
                'summary' => 'Coastal villa client interested in stone cladding and pool deck.',
                'contact_name' => 'Amina Hassan',
                'address' => [
                    'label' => 'Villa',
                    'line1' => 'Nyali Beach Road',
                    'city' => 'Mombasa',
                    'county' => 'Mombasa',
                ],
                'tags' => ['residential'],
                'groups' => ['coastal'],
            ],
        ];
    }
}
