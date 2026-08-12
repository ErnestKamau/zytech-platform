<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Company\Services\BranchService;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Company\Services\LeadershipService;
use App\Domains\Company\Services\PartnerService;
use App\Domains\Company\Services\TestimonialService;
use Illuminate\Contracts\View\View;

final class AboutPage extends BaseComponent
{
    public function render(): View
    {
        $companies = app(CompanyService::class);

        return view('livewire.website.about-page', [
            'profile' => $companies->profile(),
            'statistics' => $companies->statistics(),
            'leadership' => app(LeadershipService::class)->visible(),
            'branches' => app(BranchService::class)->all(),
            'partners' => app(PartnerService::class)->published(),
            'testimonials' => app(TestimonialService::class)->published(),
            'faqs' => $companies->faqs(),
            'certifications' => $companies->certifications(),
            'awards' => $companies->awards(),
        ]);
    }
}
