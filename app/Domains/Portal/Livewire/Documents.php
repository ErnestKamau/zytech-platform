<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\DocumentVisibility;
use App\Core\Livewire\BaseComponent;
use App\Domains\Client\Services\DocumentService;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Documents')]
final class Documents extends BaseComponent
{
    use ResolvesPortalClient;
    use WithFileUploads;

    #[Url]
    public string $search = '';

    #[Url]
    public string $kind = '';

    public string $uploadTitle = '';

    public $uploadFile = null;

    public function updatedUploadFile(): void
    {
        $this->validate([
            'uploadFile' => ['nullable', 'file', 'max:20480'],
        ]);
    }

    public function upload(DocumentService $documents): void
    {
        $this->validate([
            'uploadTitle' => ['required', 'string', 'max:255'],
            'uploadFile' => ['required', 'file', 'max:20480'],
        ]);

        $client = $this->portalClient();
        $path = $this->uploadFile->store('client-documents/'.$client->id, 'local');

        $documents->register($client, [
            'title' => $this->uploadTitle,
            'kind' => 'upload',
            'stored_path' => $path,
            'mime_type' => $this->uploadFile->getMimeType(),
            'size_bytes' => $this->uploadFile->getSize() ?: 0,
            'visibility' => DocumentVisibility::Client,
        ]);

        $this->reset('uploadTitle', 'uploadFile');
        session()->flash('status', 'Document uploaded.');
    }

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn ($document) => [
            'title' => $document->title,
            'kind' => $document->kind,
            'mime_type' => $document->mime_type,
            'created_at' => optional($document->created_at)->toDateTimeString(),
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Title', 'Kind', 'MIME', 'Created at']),
            'portal-documents.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        $documents = $this->filtered($portal);
        $kinds = $portal->documents($this->portalClient())
            ->pluck('kind')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.portal.documents', [
            'documents' => $documents,
            'kindOptions' => ['' => 'All kinds'] + $kinds->mapWithKeys(fn ($kind) => [$kind => ucfirst((string) $kind)])->all(),
        ]);
    }

    private function filtered(PortalService $portal)
    {
        return $portal->documents($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function ($document) use ($needle) {
                    return str_contains(mb_strtolower((string) $document->title), $needle)
                        || str_contains(mb_strtolower((string) $document->kind), $needle);
                });
            })
            ->when($this->kind !== '', fn ($collection) => $collection->where('kind', $this->kind))
            ->values();
    }
}
