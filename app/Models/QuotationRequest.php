<?php

namespace App\Models;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuotationRequest extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'sales_lead_id',
        'client_id',
        'lead_source_id',
        'full_name',
        'email',
        'phone',
        'project_type',
        'county',
        'location',
        'budget_range',
        'estimated_timeline',
        'description',
        'preferred_contact_method',
        'status',
        'internal_notes',
        'assigned_to',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'project_type' => ProjectType::class,
            'budget_range' => BudgetRange::class,
            'preferred_contact_method' => PreferredContactMethod::class,
            'status' => QuotationStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'quotation_request_service');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(QuotationRequestAttachment::class);
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class)->orderByDesc('scheduled_at');
    }

    public function quotation(): HasOne
    {
        return $this->hasOne(Quotation::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(QuotationStatusHistory::class)->orderByDesc('created_at');
    }
}
