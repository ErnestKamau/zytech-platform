<?php

namespace App\Domains\Company\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Company\Services\PartnerService;
use App\Models\Partner;

final class ArchivePartner extends BaseAction
{
    public function __construct(
        private readonly PartnerService $partners,
    ) {}

    public function handle(mixed ...$arguments): Partner
    {
        /** @var Partner $partner */
        $partner = $arguments[0];

        return $this->partners->archive($partner);
    }
}
