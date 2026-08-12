<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
final class Dashboard extends BaseComponent
{
    use ResolvesPortalClient;

    public function render(DashboardService $dashboard): View
    {
        return view('livewire.portal.dashboard', [
            'data' => $dashboard->forClient($this->portalClient()),
        ]);
    }
}
