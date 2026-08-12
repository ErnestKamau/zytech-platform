<?php

namespace App\Domains\Company\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Company\Services\CompanyService;
use App\Models\CompanyStatistic;
use Illuminate\Support\Collection;

final class UpdateStatistics extends BaseAction
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    /**
     * @return Collection<int, CompanyStatistic>
     */
    public function handle(mixed ...$arguments): Collection
    {
        $this->companies->forget();

        return $this->companies->statistics();
    }
}
