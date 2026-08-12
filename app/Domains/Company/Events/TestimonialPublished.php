<?php

namespace App\Domains\Company\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Testimonial;

final class TestimonialPublished extends BusinessEvent
{
    public function __construct(public Testimonial $testimonial) {}
}
