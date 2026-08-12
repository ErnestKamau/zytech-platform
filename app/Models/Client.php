<?php

namespace App\Models;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'name',
        'legal_name',
        'email',
        'phone',
        'industry',
        'website',
        'logo_key',
        'photo_key',
        'preferred_contact_method',
        'summary',
        'assigned_sales_id',
        'assigned_pm_id',
        'portal_access_granted_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'status' => ClientStatus::class,
            'preferred_contact_method' => PreferredContactMethod::class,
            'portal_access_granted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedSales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_sales_id');
    }

    public function assignedProjectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_pm_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class)->orderBy('sort_order');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class)->orderByDesc('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->orderByDesc('created_at');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(ClientTimeline::class)->orderByDesc('occurred_at');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ClientCommunication::class)->orderByDesc('occurred_at');
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(ClientPreference::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ClientStatusHistory::class)->orderByDesc('created_at');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ClientTag::class, 'client_tag', 'client_id', 'client_tag_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ClientGroup::class, 'client_group', 'client_id', 'client_group_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'client_project')
            ->withPivot('is_favorite');
    }

    public function quotationRequests(): HasMany
    {
        return $this->hasMany(QuotationRequest::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesLeads(): HasMany
    {
        return $this->hasMany(SalesLead::class);
    }
}
