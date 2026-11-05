<?php

namespace App\Domains\Project\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Domains\Project\Services\ProjectService;
use App\Models\Project;
use App\Models\Quotation;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Staff-triggered: an accepted Quotation becomes a public-site Project (Draft),
 * seeding what the quotation already knows (client, scope, location) so the
 * project can be fleshed out and published once real work starts. Never
 * automatic — see docs/QUOTATIONS.md "Deferred".
 */
final class ConvertQuotationToProject extends BaseAction
{
    public function __construct(private readonly ProjectService $projects) {}

    /**
     * @param  array{project_category_id: string, title?: string}  $arguments
     */
    public function handle(mixed ...$arguments): Project
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];
        /** @var array{project_category_id: string, title?: string} $input */
        $input = $arguments[1];

        if ($quotation->converted_project_id !== null) {
            return Project::query()->findOrFail($quotation->converted_project_id);
        }

        if ($quotation->status !== QuotationStatus::Accepted) {
            throw new DomainException('Only an accepted quotation can be converted to a project.');
        }

        return DB::transaction(function () use ($quotation, $input): Project {
            $quotation->loadMissing(['request', 'client']);
            $request = $quotation->request;

            $project = $this->projects->create([
                'project_category_id' => $input['project_category_id'],
                'title' => $input['title'] ?? $quotation->title,
                'status' => ProjectStatus::Draft,
                'construction_stage' => ConstructionStage::Planning,
                'client_name' => $quotation->client?->name,
                'type' => $request?->project_type ?? ProjectType::Commercial,
                'county' => $request?->county,
                'location_label' => $request?->location,
                'excerpt' => $quotation->notes,
            ]);

            $quotation->forceFill(['converted_project_id' => $project->id])->save();

            return $project;
        });
    }
}
