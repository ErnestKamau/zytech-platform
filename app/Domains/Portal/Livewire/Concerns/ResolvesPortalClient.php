<?php

namespace App\Domains\Portal\Livewire\Concerns;

use App\Domains\Portal\Repositories\PortalRepository;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

trait ResolvesPortalClient
{
    protected function portalClient(): Client
    {
        $fromRequest = request()->attributes->get('portalClient');

        if ($fromRequest instanceof Client) {
            return $fromRequest;
        }

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $client = app(PortalRepository::class)->clientForUser($user);
        abort_unless($client !== null, 403);

        return $client;
    }
}
