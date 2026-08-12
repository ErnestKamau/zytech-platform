<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Projects')]
final class Projects extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function toggleFavorite(string $projectId, PortalService $portal): void
    {
        $client = $this->portalClient();
        $project = $portal->projects($client)->firstWhere('id', $projectId);
        abort_unless($project !== null, 404);

        $saved = $portal->toggleFavorite($client, $project);
        session()->flash('status', $saved ? 'Project saved.' : 'Project removed from saved.');
    }

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn (Project $project) => [
            'title' => $project->title,
            'status' => method_exists($project, 'statusLabel') ? $project->statusLabel() : (string) $project->status,
            'slug' => $project->slug,
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Title', 'Status', 'Slug']),
            'portal-projects.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        $projects = $this->filtered($portal);
        $statusOptions = ['' => 'All statuses'] + $portal->projects($this->portalClient())
            ->map(fn (Project $project) => method_exists($project, 'statusLabel') ? $project->statusLabel() : (string) $project->status)
            ->filter()
            ->unique()
            ->sort()
            ->mapWithKeys(fn ($label) => [$label => $label])
            ->all();

        return view('livewire.portal.projects', [
            'projects' => $projects,
            'featured' => $projects->take(2),
            'statusOptions' => $statusOptions,
        ]);
    }

    private function filtered(PortalService $portal)
    {
        return $portal->projects($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(
                    fn (Project $project) => str_contains(mb_strtolower((string) $project->title), $needle)
                );
            })
            ->when($this->status !== '', function ($collection) {
                return $collection->filter(function (Project $project) {
                    $label = method_exists($project, 'statusLabel') ? $project->statusLabel() : (string) $project->status;

                    return $label === $this->status;
                });
            })
            ->values();
    }
}
