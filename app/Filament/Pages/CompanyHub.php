<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Awards\AwardResource;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\Certifications\CertificationResource;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\CompanyStatistics\CompanyStatisticResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\LeadershipMembers\LeadershipMemberResource;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\CompanyStatistic;
use Filament\Support\Icons\Heroicon;

class CompanyHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Company';

    protected static ?string $title = 'Company';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 6;

    protected function getHubSubheading(): ?string
    {
        return 'Profile, team, credibility, and CMS statistics.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Profile',
                'cards' => [
                    $this->hubCard('Company', CompanyResource::class, Heroicon::OutlinedBuildingOffice2),
                    $this->hubCard('Branches', BranchResource::class, Heroicon::OutlinedMapPin),
                    $this->hubCard('Leadership', LeadershipMemberResource::class, Heroicon::OutlinedUserGroup),
                    $this->hubCard('Statistics', CompanyStatisticResource::class, Heroicon::OutlinedChartBar, count: CompanyStatistic::query()->count()),
                ],
            ],
            [
                'title' => 'Credibility',
                'cards' => [
                    $this->hubCard('Partners', PartnerResource::class, Heroicon::OutlinedHandRaised),
                    $this->hubCard('Certifications', CertificationResource::class, Heroicon::OutlinedShieldCheck),
                    $this->hubCard('Awards', AwardResource::class, Heroicon::OutlinedTrophy),
                    $this->hubCard('Testimonials', TestimonialResource::class, Heroicon::OutlinedChatBubbleBottomCenterText),
                    $this->hubCard('FAQs', FaqResource::class, Heroicon::OutlinedQuestionMarkCircle),
                ],
            ],
        ];
    }
}
