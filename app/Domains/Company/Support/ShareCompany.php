<?php

namespace App\Domains\Company\Support;

use App\Domains\Company\Data\CompanyData;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Company\Services\TestimonialService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareCompany
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('companyProfile', null);
            $view->with('companyStatistics', collect());
            $view->with('homeTestimonials', collect());
            $view->with('homeFaqs', collect());

            return;
        }

        try {
            $view->with('companyProfile', $this->companies->profile());
            $view->with('companyStatistics', $this->companies->statistics());
            $view->with('homeTestimonials', app(TestimonialService::class)->published());
            $view->with('homeFaqs', $this->companies->faqs());
        } catch (\Throwable) {
            $view->with('companyProfile', null);
            $view->with('companyStatistics', collect());
            $view->with('homeTestimonials', collect());
            $view->with('homeFaqs', collect());
        }
    }

    /**
     * @param  array<string, mixed>  $platformContact
     * @return array{email: string, phone: string, whatsapp: string, location: string, service_area: string}
     */
    public static function contact(?CompanyData $profile, array $platformContact = []): array
    {
        return [
            'email' => $profile?->email ?: (string) ($platformContact['email'] ?? 'hello@zytech.co.ke'),
            'phone' => $profile?->phone ?: (string) ($platformContact['phone'] ?? '+254 700 000 000'),
            'whatsapp' => $profile?->whatsapp ?: (string) ($platformContact['whatsapp'] ?? ''),
            'location' => $profile?->location ?: (string) ($platformContact['location'] ?? 'Nairobi, Kenya'),
            'service_area' => $profile?->serviceArea ?: (string) ($platformContact['service_area'] ?? 'Serving Nairobi, Kiambu, and nationwide'),
        ];
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('companies');
        } catch (\Throwable) {
            return false;
        }
    }
}
