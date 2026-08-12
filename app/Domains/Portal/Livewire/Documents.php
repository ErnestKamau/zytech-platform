<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\DownloadDocument;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Documents')]
final class Documents extends BaseComponent
{
    use ResolvesPortalClient;

    public function download(string $id, PortalService $portal, DownloadDocument $action): void
    {
        $client = $this->portalClient();
        $document = $portal->documents($client)->firstWhere('id', $id);
        abort_unless($document !== null, 404);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $action->handle($client, $user, $document);
        session()->flash('status', 'Download recorded for “'.$document->title.'”.');
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.documents', [
            'documents' => $portal->documents($this->portalClient()),
        ]);
    }
}
