<?php

namespace App\Domains\Quotation\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Quotation\Services\SiteVisitService;
use App\Models\SiteVisit;

final class ScheduleSiteVisit extends BaseAction
{
    public function __construct(private readonly SiteVisitService $visits) {}

    public function handle(mixed ...$arguments): SiteVisit
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $arguments[0];

        return $this->visits->schedule($attributes);
    }
}
