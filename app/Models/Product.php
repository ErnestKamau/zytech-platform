<?php

namespace App\Models;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasPublishedState;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseModel
{
    use HasActivity;
    use HasPublishedState;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = [
        'product_category_id',
        'brand_id',
        'default_unit_id',
        'title',
        'slug',
        'sku',
        'excerpt',
        'body',
        'icon_path',
        'image_key',
        'gallery_keys',
        'specifications',
        'unit_of_measure',
        'purchase_mode',
        'status',
        'visibility',
        'pricing_model',
        'price_amount',
        'price_currency',
        'price_unit',
        'pricing_notes',
        'taxable',
        'stock_display',
        'is_featured',
        'meta_title',
        'meta_description',
        'og_image_key',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'gallery_keys' => 'array',
            'specifications' => 'array',
            'purchase_mode' => PurchaseMode::class,
            'status' => ProductStatus::class,
            'visibility' => VisibilityStatus::class,
            'pricing_model' => PricingModel::class,
            'price_amount' => 'decimal:2',
            'taxable' => 'boolean',
            'stock_display' => 'integer',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', VisibilityStatus::Public);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function defaultUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function quotationRequests(): BelongsToMany
    {
        return $this->belongsToMany(QuotationRequest::class, 'quotation_request_product');
    }
}
