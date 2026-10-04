<?php

namespace App\Domains\Website\Livewire;

use App\Core\Enums\ProjectType;
use App\Core\Livewire\BaseComponent;
use App\Domains\Quotation\Actions\SubmitQuotationRequest;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

final class RequestQuotationForm extends BaseComponent
{
    use WithFileUploads;

    public string $fullName = '';

    public string $email = '';

    public string $phone = '';

    public string $projectType = 'residential';

    public string $county = 'Nairobi';

    public string $location = '';

    public string $description = '';

    /** @var list<string> */
    public array $selectedServices = [];

    /**
     * Pre-filled from a product page link (?product=slug); not editable on the form.
     *
     * @var list<string>
     */
    #[Locked]
    public array $selectedProducts = [];

    /** @var list<TemporaryUploadedFile> */
    public array $attachments = [];

    public function mount(?string $product = null): void
    {
        if ($product === null || $product === '') {
            $product = request()->query('product');
        }

        if (! is_string($product) || $product === '') {
            return;
        }

        $matched = Product::query()
            ->published()
            ->public()
            ->where('slug', $product)
            ->first();

        if ($matched === null) {
            return;
        }

        $this->selectedProducts = [$matched->id];

        if ($this->description === '') {
            $this->description = 'Interested in product: '.$matched->title
                .($matched->sku ? ' (SKU '.$matched->sku.')' : '')
                .'. Please quote quantities and delivery.';
        }
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'projectType' => ['required', 'string'],
            'county' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'selectedServices' => ['array'],
            'selectedServices.*' => ['uuid', 'exists:services,id'],
            'selectedProducts' => ['array'],
            'selectedProducts.*' => ['uuid', 'exists:products,id'],
            'attachments' => ['array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip'],
        ]);

        $request = app(SubmitQuotationRequest::class)->handle(
            [
                'full_name' => $validated['fullName'],
                'email' => $validated['email'],
                'phone' => $this->phone ?: null,
                'project_type' => ProjectType::from($this->projectType),
                'county' => $this->county,
                'location' => $this->location ?: null,
                'description' => $this->description,
            ],
            $this->selectedServices,
            $this->attachments,
            $this->selectedProducts,
        );

        $this->redirectRoute('quote.success', ['reference' => $request->reference_number], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.website.request-quotation-form', [
            'services' => Service::query()->published()->public()->orderBy('title')->get(),
            'projectTypes' => ProjectType::cases(),
        ]);
    }
}
