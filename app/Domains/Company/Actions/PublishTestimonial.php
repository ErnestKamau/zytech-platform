<?php

namespace App\Domains\Company\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Company\Services\TestimonialService;
use App\Models\Testimonial;

final class PublishTestimonial extends BaseAction
{
    public function __construct(
        private readonly TestimonialService $testimonials,
    ) {}

    public function handle(mixed ...$arguments): Testimonial
    {
        /** @var Testimonial $testimonial */
        $testimonial = $arguments[0];

        return $this->testimonials->publish($testimonial);
    }
}
