<?php

namespace App\Domains\Website\Livewire;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\ProjectType;
use App\Core\Livewire\BaseComponent;
use App\Domains\Quotation\Actions\SubmitQuotationRequest;
use App\Models\Service;
use Illuminate\Contracts\View\View;
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

    public string $budgetRange = 'undecided';

    public string $estimatedTimeline = '';

    public string $description = '';

    public string $preferredContactMethod = 'email';

    /** @var list<string> */
    public array $selectedServices = [];

    /** @var list<TemporaryUploadedFile> */
    public array $attachments = [];

    public function submit(): void
    {
        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'projectType' => ['required', 'string'],
            'county' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'budgetRange' => ['required', 'string'],
            'estimatedTimeline' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:10000'],
            'preferredContactMethod' => ['required', 'string'],
            'selectedServices' => ['array'],
            'selectedServices.*' => ['uuid'],
            'attachments' => ['array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip'],
        ]);

        $request = app(SubmitQuotationRequest::class)->handle(
            [
                'full_name' => $validated['fullName'] ?? $this->fullName,
                'email' => $validated['email'] ?? $this->email,
                'phone' => $this->phone ?: null,
                'project_type' => ProjectType::from($this->projectType),
                'county' => $this->county,
                'location' => $this->location ?: null,
                'budget_range' => BudgetRange::from($this->budgetRange),
                'estimated_timeline' => $this->estimatedTimeline ?: null,
                'description' => $this->description,
                'preferred_contact_method' => PreferredContactMethod::from($this->preferredContactMethod),
            ],
            $this->selectedServices,
            $this->attachments,
        );

        $this->redirectRoute('quote.success', ['reference' => $request->reference_number], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.website.request-quotation-form', [
            'services' => Service::query()->published()->public()->orderBy('title')->get(),
            'projectTypes' => ProjectType::cases(),
            'budgetRanges' => BudgetRange::cases(),
            'contactMethods' => PreferredContactMethod::cases(),
        ]);
    }
}
