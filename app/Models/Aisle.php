<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aisle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'tenant_id',
        'is_global'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_global' => 'boolean',
    ];

    /**
     * Get the tenant that owns the aisle.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Lien vers une catégorie (optionnel)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Produits dont l'emplacement recommandé est cet aisle (cette relation n'est plus utilisée)
     * @deprecated
     */
    public function products()
    {
        // Cette relation n'est plus utilisée car les produits sont désormais globaux et n'ont pas d'emplacement spécifique
        // L'emplacement est défini au niveau du tenant_inventory
        return $this->hasMany(Product::class, 'aisle_id');
    }

    /**
     * Inventaires de tenant utilisant cet aisle
     */
    public function tenantInventories(): HasMany
    {
        return $this->hasMany(TenantInventory::class, 'aisle_id');
    }

    /**
     * Scope a query to only include aisles for a given tenant.
     */
    public function scopeTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope a query to only include global aisles.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope a query to include all aisles available for a tenant (both tenant-specific and global).
     */
    public function scopeAvailableFor($query, $tenantId)
    {
        return $query->where('is_global', true)
                    ->orWhere('tenant_id', $tenantId);
    }

}
