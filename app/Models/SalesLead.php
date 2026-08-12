<?php

namespace App\Models;

use App\Core\Enums\LeadStatus;
use App\Core\Enums\PriorityLevel;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesLead extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'lead_source_id',
        'full_name',
        'email',
        'phone',
        'company_name',
        'status',
        'priority',
        'notes',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'priority' => PriorityLevel::class,
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function quotationRequests(): HasMany
    {
        return $this->hasMany(QuotationRequest::class)->orderByDesc('submitted_at');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->orderByDesc('created_at');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class)->orderByDesc('scheduled_at');
    }
}
