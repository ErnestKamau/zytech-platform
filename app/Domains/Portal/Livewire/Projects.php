<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Projects')]
final class Projects extends BaseComponent
{
    use ResolvesPortalClient;

    public function toggleFavorite(string $projectId, PortalService $portal): void
    {
        $client = $this->portalClient();
        $project = $portal->projects($client)->firstWhere('id', $projectId);
        abort_unless($project !== null, 404);

        $saved = $portal->toggleFavorite($client, $project);
        session()->flash('status', $saved ? 'Project saved.' : 'Project removed from saved.');
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.projects', [
            'projects' => $portal->projects($this->portalClient()),
        ]);
    }
}
