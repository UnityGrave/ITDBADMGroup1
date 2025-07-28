<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Currency Model for EPIC 8: Multi-Currency Support
 * 
 * Represents a currency with exchange rates and formatting rules
 */
class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_active',
        'is_base_currency',
        'formatting_rules',
        'rate_updated_at',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_active' => 'boolean',
        'is_base_currency' => 'boolean',
        'formatting_rules' => 'array',
        'rate_updated_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Get products that use this currency as their base currency
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'base_currency_code', 'code');
    }

    /**
     * Get price overrides for this currency
     */
    public function priceOverrides(): HasMany
    {
        return $this->hasMany(ProductPriceOverride::class, 'currency_code', 'code');
    }

    /**
     * Scope to get only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get the base currency
     */
    public function scopeBase($query)
    {
        return $query->where('is_base_currency', true);
    }

    /**
     * Get the base currency
     */
    public static function getBaseCurrency(): ?Currency
    {
        return static::base()->first();
    }

    /**
     * Convert amount from base currency to this currency
     */
    public function convertFromBase(int $amountInCents): int
    {
        return (int) round($amountInCents * $this->exchange_rate);
    }

    /**
     * Convert amount from this currency to base currency
     */
    public function convertToBase(int $amountInCents): int
    {
        if ($this->exchange_rate == 0) {
            return 0;
        }
        return (int) round($amountInCents / $this->exchange_rate);
    }

    /**
     * Format amount in cents to human-readable format
     */
    public function formatAmount(int $amountInCents, bool $includeSymbol = true): string
    {
        $decimalPlaces = $this->getDecimalPlaces();
        $amount = $amountInCents / (10 ** $decimalPlaces);
        
        $formatted = number_format($amount, $decimalPlaces, '.', ',');
        
        if ($includeSymbol) {
            return $this->symbol . $formatted;
        }
        
        return $formatted;
    }

    /**
     * Get the number of decimal places for this currency
     */
    public function getDecimalPlaces(): int
    {
        if (isset($this->formatting_rules['decimal_places'])) {
            return $this->formatting_rules['decimal_places'];
        }

        // Default to 2 decimal places for most currencies
        // Some currencies like JPY have 0 decimal places
        return match ($this->code) {
            'JPY', 'KRW', 'VND' => 0,
            default => 2,
        };
    }

    /**
     * Check if the exchange rate is outdated
     */
    public function isExchangeRateOutdated(): bool
    {
        if (!$this->rate_updated_at) {
            return true;
        }

        return $this->rate_updated_at->lt(now()->subDay());
    }

    /**
     * Update exchange rate
     */
    public function updateExchangeRate(float $newRate): void
    {
        $this->update([
            'exchange_rate' => $newRate,
            'rate_updated_at' => now(),
        ]);
    }
} 