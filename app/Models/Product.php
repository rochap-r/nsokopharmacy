<?php

namespace App\Models;

use App\Enums\PersonType;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dci',
        'dosage',
        'forme_galenique',
        'color_code',
        'type',
        'personne',
        'category_id',
        'aisle_id',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ProductType::class,
        'personne' => PersonType::class,
        'active' => 'boolean',
    ];

    /**
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the default aisle recommendation for this product (global).
     */
    public function aisle(): BelongsTo
    {
        return $this->belongsTo(Aisle::class);
    }

    /**
     * Get the tenant inventories for this product.
     */
    public function tenantInventories(): HasMany
    {
        return $this->hasMany(TenantInventory::class);
    }

    /**
     * Format medication details (DCI, dosage, forme_galenique) as a readable string.
     */
    public function getFullNameAttribute(): string
    {
        $parts = [$this->dci];

        if ($this->dosage) {
            $parts[] = $this->dosage;
        }

        if ($this->forme_galenique) {
            $parts[] = $this->forme_galenique;
        }

        return implode(' - ', $parts);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
