<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Activity')]
final class Timeline extends BaseComponent
{
    use ResolvesPortalClient;

    public function render(): View
    {
        /** @var Client $client */
        $client = $this->portalClient()->load(['timeline' => fn ($q) => $q->limit(40)]);

        return view('livewire.portal.timeline', [
            'events' => $client->timeline,
        ]);
    }
}
