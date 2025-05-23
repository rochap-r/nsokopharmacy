<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'domain',
        'phone',
        'email',
        'address',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the users for the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }



    /**
     * Get the roles for the tenant.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Les catégories sont globales - cette relation n'est plus valide
     * @deprecated
     */
    public function categories()
    {
        // Cette relation n'est plus valide car les catégories sont globales
        // Cette méthode est conservée pour compatibilité arrière mais ne retourne rien
        return null;
    }

    /**
     * Get the aisles for the tenant.
     */
    public function aisles(): HasMany
    {
        return $this->hasMany(Aisle::class);
    }

    /**
     * Get the inventory items for the tenant.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(TenantInventory::class);
    }

    /**
     * Les produits sont globaux mais on peut accéder aux produits disponibles pour ce tenant
     * via les inventaires.
     */
    public function products()
    {
        // Retourne les produits disponibles pour ce tenant via une relation à travers inventories
        return Product::whereHas('tenantInventories', function($query) {
            $query->where('tenant_id', $this->id);
        });
    }
}
