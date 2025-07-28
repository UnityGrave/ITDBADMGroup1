<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * ProductPriceOverride Model for EPIC 8: Multi-Currency Support
 * 
 * Represents currency-specific price overrides for products
 */
class ProductPriceOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'currency_code',
        'price_cents',
        'is_active',
        'notes',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Get the product that owns this price override
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the currency for this price override
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Scope to get only active overrides
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get overrides for a specific currency
     */
    public function scopeForCurrency($query, string $currencyCode)
    {
        return $query->where('currency_code', $currencyCode);
    }

    /**
     * Scope to get overrides for a specific product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to get currently effective overrides
     */
    public function scopeCurrentlyEffective($query)
    {
        $now = now();
        
        return $query->where(function ($q) use ($now) {
            $q->where(function ($subQ) use ($now) {
                // No effective_from date, or effective_from is in the past/now
                $subQ->whereNull('effective_from')
                     ->orWhere('effective_from', '<=', $now);
            })->where(function ($subQ) use ($now) {
                // No effective_until date, or effective_until is in the future
                $subQ->whereNull('effective_until')
                     ->orWhere('effective_until', '>', $now);
            });
        });
    }

    /**
     * Check if this override is currently effective
     */
    public function isCurrentlyEffective(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Check effective_from
        if ($this->effective_from && $this->effective_from->gt($now)) {
            return false;
        }

        // Check effective_until
        if ($this->effective_until && $this->effective_until->lte($now)) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted price using the currency's formatting rules
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency->formatAmount($this->price_cents);
    }

    /**
     * Get price as decimal value (for display purposes)
     */
    public function getPriceAsDecimalAttribute(): float
    {
        $decimalPlaces = $this->currency->getDecimalPlaces();
        return $this->price_cents / (10 ** $decimalPlaces);
    }

    /**
     * Set price from decimal value
     */
    public function setPriceFromDecimal(float $price): void
    {
        $decimalPlaces = $this->currency->getDecimalPlaces();
        $this->price_cents = (int) round($price * (10 ** $decimalPlaces));
    }

    /**
     * Get the effective override for a product in a specific currency with caching
     */
    public static function getEffectiveOverride(int $productId, string $currencyCode): ?self
    {
        $cacheKey = "price_override_{$productId}_{$currencyCode}";
        
        return cache()->remember($cacheKey, 300, function () use ($productId, $currencyCode) {
            return static::forProduct($productId)
                ->forCurrency($currencyCode)
                ->active()
                ->currentlyEffective()
                ->first();
        });
    }

    /**
     * Create or update a price override
     */
    public static function setOverride(
        int $productId,
        string $currencyCode,
        int $priceCents,
        ?Carbon $effectiveFrom = null,
        ?Carbon $effectiveUntil = null,
        ?string $notes = null
    ): self {
        $override = static::updateOrCreate(
            [
                'product_id' => $productId,
                'currency_code' => $currencyCode,
            ],
            [
                'price_cents' => $priceCents,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_until' => $effectiveUntil,
                'notes' => $notes,
            ]
        );
        
        // Clear related caches
        cache()->forget("price_override_{$productId}_{$currencyCode}");
        cache()->forget("product_price_calc_{$productId}_{$currencyCode}");
        cache()->forget("product_price_{$productId}_{$currencyCode}");
        
        return $override;
    }

    /**
     * Deactivate this override
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate this override
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
} 