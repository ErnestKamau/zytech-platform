<?php

namespace Database\Seeders;

use App\Core\Enums\AwardCategory;
use App\Core\Enums\BranchType;
use App\Core\Enums\CertificationStatus;
use App\Core\Enums\CompanyStatus;
use App\Core\Enums\NavigationLocation;
use App\Domains\Company\Services\CompanyService;
use App\Models\Award;
use App\Models\Branch;
use App\Models\Certification;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Faq;
use App\Models\LeadershipMember;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Partner;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = $this->seedCompany();
        $this->seedBranches($company);
        $this->seedLeadership($company);
        $this->seedStatistics($company);
        $this->seedTestimonials($company);
        $this->seedFaqs($company);
        $this->seedCertifications($company);
        $this->seedAwards($company);
        $this->seedPartners($company);
        $this->seedAboutNavigation();

        app(CompanyService::class)->forget();
    }

    private function seedCompany(): Company
    {
        return Company::query()->updateOrCreate(
            ['slug' => 'zytech-contractors'],
            [
                'name' => 'Zytech Contractors',
                'registered_name' => 'Zytech Contractors Limited',
                'tagline' => 'Built on Kenyan soil',
                'motto' => 'If you can plan it, we can build it.',
                'short_description' => 'Precision-built spaces for residential and commercial clients across Nairobi, Kiambu, and nationwide — from first sketch to final handover.',
                'about' => 'Zytech Contractors is a Kenyan construction firm delivering interior, exterior, and structural work with one accountable crew. We design, estimate, approve, and build — no hand-offs, no finger-pointing.',
                'mission' => 'Deliver durable, well-managed builds on Kenyan soil, from first sketch to final handover.',
                'vision' => 'Be the contractor principals call when the work has to be right the first time.',
                'history' => 'Zytech grew from site crews in Nairobi and Kiambu into a full-discipline contractor: architecture, interiors, structure, and finishing under one roof.',
                'why_choose_us' => 'One team owns the drawing, the estimate, the site, and the handover. You speak to the people who will still be there when the keys are handed over.',
                'core_values' => ['Accountability', 'Craft', 'On-time delivery', 'Kenyan materials first'],
                'email' => 'hello@zytech.co.ke',
                'phone' => '+254 700 000 000',
                'whatsapp' => '+254 700 000 000',
                'location' => 'Nairobi, Kenya',
                'service_area' => 'Serving Nairobi, Kiambu, and nationwide',
                'website' => 'https://zytech.co.ke',
                'status' => CompanyStatus::Published,
                'published_at' => now(),
            ],
        );
    }

    private function seedBranches(Company $company): void
    {
        $branches = [
            [
                'name' => 'Nairobi headquarters',
                'type' => BranchType::Headquarters,
                'address' => 'Westlands',
                'city' => 'Nairobi',
                'county' => 'Nairobi',
                'phone' => '+254 700 000 000',
                'email' => 'hello@zytech.co.ke',
                'is_primary' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Kiambu site office',
                'type' => BranchType::SiteOffice,
                'address' => 'Kiambu Road',
                'city' => 'Kiambu',
                'county' => 'Kiambu',
                'phone' => '+254 700 000 001',
                'email' => 'kiambu@zytech.co.ke',
                'is_primary' => false,
                'sort_order' => 1,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $branch['name'],
                ],
                $branch,
            );
        }
    }

    private function seedLeadership(Company $company): void
    {
        $members = [
            [
                'name' => 'Ernest Mwangi',
                'position' => 'Managing Director',
                'biography' => 'Leads delivery and client relationships across Nairobi and Kiambu projects.',
                'sort_order' => 0,
            ],
            [
                'name' => 'Amina Otieno',
                'position' => 'Head of Design',
                'biography' => 'Owns architecture and interior packages from concept through site instruction.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Daniel Kariuki',
                'position' => 'Construction Manager',
                'biography' => 'Runs site crews, materials, and programme on active builds.',
                'sort_order' => 2,
            ],
        ];

        foreach ($members as $member) {
            LeadershipMember::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $member['name'],
                ],
                [
                    ...$member,
                    'is_visible' => true,
                ],
            );
        }
    }

    private function seedStatistics(Company $company): void
    {
        $stats = [
            ['label' => 'Projects delivered', 'value' => '120+', 'sort_order' => 0],
            ['label' => 'Years in operation', 'value' => '14', 'sort_order' => 1],
            ['label' => 'On-time completion', 'value' => '96%', 'sort_order' => 2],
            ['label' => 'Value under management', 'value' => 'KES 2.4B', 'sort_order' => 3],
        ];

        foreach ($stats as $stat) {
            CompanyStatistic::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'label' => $stat['label'],
                ],
                [
                    ...$stat,
                    'is_visible' => true,
                ],
            );
        }
    }

    private function seedTestimonials(Company $company): void
    {
        $quotes = [
            [
                'author_name' => 'Grace Wanjiku',
                'author_role' => 'Principal',
                'company_name' => 'Wanjiku Residences',
                'quote' => 'They finished the courtyard to the drawing — and the programme. That is rare.',
                'is_featured' => true,
                'sort_order' => 0,
            ],
            [
                'author_name' => 'Peter Njoroge',
                'author_role' => 'Facilities lead',
                'company_name' => 'Ridge Commercial',
                'quote' => 'One crew from ballast to handover. We never had to chase a subcontractor.',
                'is_featured' => false,
                'sort_order' => 1,
            ],
        ];

        foreach ($quotes as $quote) {
            Testimonial::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'author_name' => $quote['author_name'],
                ],
                [
                    ...$quote,
                    'status' => CompanyStatus::Published,
                    'published_at' => now(),
                ],
            );
        }
    }

    private function seedFaqs(Company $company): void
    {
        $faqs = [
            [
                'question' => 'Where do you work?',
                'answer' => 'Nairobi, Kiambu, and nationwide for larger commercial and residential programmes.',
                'sort_order' => 0,
            ],
            [
                'question' => 'Do you handle design and build?',
                'answer' => 'Yes. Architecture, interiors, structure, and finishing sit with one team so the drawing matches the site.',
                'sort_order' => 1,
            ],
            [
                'question' => 'How do quotations work?',
                'answer' => 'Share the brief or drawings. We visit the site, issue a scoped estimate, and confirm programme before mobilisation.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Can we visit a live site?',
                'answer' => 'With the client’s permission, yes. Contact us and we will arrange a supervised visit.',
                'sort_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'question' => $faq['question'],
                ],
                [
                    ...$faq,
                    'is_published' => true,
                ],
            );
        }
    }

    private function seedCertifications(Company $company): void
    {
        $certs = [
            [
                'name' => 'NCA contractor registration',
                'issuer' => 'National Construction Authority',
                'status' => CertificationStatus::Active,
                'sort_order' => 0,
            ],
            [
                'name' => 'Occupational safety compliance',
                'issuer' => 'DOSHS',
                'status' => CertificationStatus::Active,
                'sort_order' => 1,
            ],
        ];

        foreach ($certs as $cert) {
            Certification::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $cert['name'],
                ],
                $cert,
            );
        }
    }

    private function seedAwards(Company $company): void
    {
        Award::query()->updateOrCreate(
            [
                'company_id' => $company->id,
                'title' => 'Regional craft excellence',
            ],
            [
                'category' => AwardCategory::Industry,
                'year' => 2024,
                'issuer' => 'Kenya construction forum',
                'description' => 'Recognised for on-site finishing quality on commercial courtyard work.',
                'sort_order' => 0,
            ],
        );
    }

    private function seedPartners(Company $company): void
    {
        $partners = [
            [
                'name' => 'Kenya steel supplies',
                'description' => 'Structural steel and reinforcement for walkways and frames.',
                'sort_order' => 0,
            ],
            [
                'name' => 'Rift stone & paving',
                'description' => 'Natural stone and interlocking pavers for courtyards and hardscape.',
                'sort_order' => 1,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $partner['name'],
                ],
                [
                    ...$partner,
                    'is_published' => true,
                ],
            );
        }
    }

    private function seedAboutNavigation(): void
    {
        $menus = NavigationMenu::query()
            ->whereIn('location', [NavigationLocation::Header, NavigationLocation::Footer])
            ->get();

        foreach ($menus as $menu) {
            NavigationItem::query()->where('navigation_menu_id', $menu->id)
                ->where('route_name', 'contact')
                ->update(['sort_order' => 4]);

            NavigationItem::query()->updateOrCreate(
                [
                    'navigation_menu_id' => $menu->id,
                    'route_name' => 'about',
                ],
                [
                    'label' => 'About',
                    'url' => null,
                    'target' => '_self',
                    'is_visible' => true,
                    'sort_order' => 3,
                ],
            );
        }
    }
}
