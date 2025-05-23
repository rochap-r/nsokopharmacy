<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantInventory extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'product_id',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'alert_threshold',
        'aisle_id',
        'expiry_date',
        'last_restock_date',
        'available_for_sale'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'alert_threshold' => 'integer',
        'expiry_date' => 'date',
        'last_restock_date' => 'date',
        'available_for_sale' => 'boolean'
    ];
    
    /**
     * Get the tenant that owns this inventory.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    /**
     * Get the product that this inventory is for.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the aisle where this product is located.
     */
    public function aisle(): BelongsTo
    {
        return $this->belongsTo(Aisle::class);
    }
    
    /**
     * Scope a query to only include inventory for a given tenant.
     */
    public function scopeTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
    
    /**
     * Scope a query to only include items with low stock (below threshold).
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'alert_threshold');
    }
    
    /**
     * Scope a query to only include items with expiring products.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        $date = now()->addDays($days);
        return $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<=', $date);
    }
    
    /**
     * Scope a query to only include available items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_for_sale', true)
                    ->where('stock_quantity', '>', 0);
    }
}
