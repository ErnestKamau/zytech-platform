<?php

namespace App\Models;

use App\Core\Enums\CompanyStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends BaseModel
{
    use HasActivity;
    use HasPublishedState;
    use HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'registered_name',
        'slug',
        'tagline',
        'motto',
        'short_description',
        'about',
        'mission',
        'vision',
        'history',
        'why_choose_us',
        'core_values',
        'email',
        'phone',
        'whatsapp',
        'location',
        'service_area',
        'website',
        'registration_number',
        'tax_number',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'core_values' => 'array',
            'status' => CompanyStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class)->orderBy('sort_order');
    }

    public function leadershipMembers(): HasMany
    {
        return $this->hasMany(LeadershipMember::class)->orderBy('sort_order');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class)->orderBy('sort_order');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class)->orderBy('sort_order');
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class)->orderBy('sort_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(CompanyStatistic::class)->orderBy('sort_order');
    }
}
