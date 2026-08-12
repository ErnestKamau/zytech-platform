<?php

namespace App\Domains\Company\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Certification;

final class CertificationUpdated extends BusinessEvent
{
    public function __construct(public Certification $certification) {}
}
