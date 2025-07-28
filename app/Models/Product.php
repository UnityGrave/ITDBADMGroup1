<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Enums\ProductCondition;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'card_id',
        'condition',
        'base_price_cents',
        'base_currency_code',
        'sku',
    ];

    protected $casts = [
        'condition' => ProductCondition::class,
        'base_price_cents' => 'integer',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $this->load(['card.set', 'card.rarity', 'card.category', 'inventory', 'baseCurrency']);

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'condition' => $this->condition->value,
            'base_price_cents' => $this->base_price_cents,
            'base_currency_code' => $this->base_currency_code,
            'price_decimal' => $this->getBasePriceAsDecimal(),
            'stock' => $this->inventory?->stock ?? 0,
            // Card details
            'card_name' => $this->card->name,
            'collector_number' => $this->card->collector_number,
            // Set details
            'set_name' => $this->card->set->name,
            // Rarity details
            'rarity_name' => $this->card->rarity->name,
            // Category details
            'category_name' => $this->card->category->name,
            // Combined searchable text
            'searchable_text' => implode(' ', [
                $this->card->name,
                $this->card->set->name,
                $this->card->rarity->name,
                $this->card->category->name,
                $this->condition->value,
                $this->sku,
            ]),
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        // Only make products searchable if they have stock or are visible
        return $this->inventory && $this->inventory->stock > 0;
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get the base currency for this product
     */
    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency_code', 'code');
    }

    /**
     * Get price overrides for this product
     */
    public function priceOverrides(): HasMany
    {
        return $this->hasMany(ProductPriceOverride::class);
    }

    /**
     * Get active price overrides for this product
     */
    public function activePriceOverrides(): HasMany
    {
        return $this->priceOverrides()->active()->currentlyEffective();
    }

    /**
     * Get base price as decimal value
     */
    public function getBasePriceAsDecimal(): float
    {
        if (!$this->baseCurrency) {
            $this->load('baseCurrency');
        }
        
        $decimalPlaces = $this->baseCurrency->getDecimalPlaces();
        return $this->base_price_cents / (10 ** $decimalPlaces);
    }

    /**
     * Set base price from decimal value
     */
    public function setBasePriceFromDecimal(float $price): void
    {
        if (!$this->baseCurrency) {
            $this->load('baseCurrency');
        }
        
        $decimalPlaces = $this->baseCurrency->getDecimalPlaces();
        $this->base_price_cents = (int) round($price * (10 ** $decimalPlaces));
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePriceAttribute(): string
    {
        return $this->baseCurrency->formatAmount($this->base_price_cents);
    }

    /**
     * Get price in a specific currency (with override support)
     */
    public function getPriceInCurrency(string $currencyCode): int
    {
        // Check for price override first
        $override = ProductPriceOverride::getEffectiveOverride($this->id, $currencyCode);
        if ($override) {
            return $override->price_cents;
        }

        // Convert from base currency
        $targetCurrency = Currency::where('code', $currencyCode)->first();
        if (!$targetCurrency) {
            throw new \InvalidArgumentException("Currency {$currencyCode} not found");
        }

        // If requesting the same currency as base, return base price
        if ($currencyCode === $this->base_currency_code) {
            return $this->base_price_cents;
        }

        return $targetCurrency->convertFromBase($this->base_price_cents);
    }

    /**
     * Get formatted price in a specific currency
     */
    public function getFormattedPriceInCurrency(string $currencyCode): string
    {
        $priceInCents = $this->getPriceInCurrency($currencyCode);
        $currency = Currency::where('code', $currencyCode)->first();
        
        return $currency->formatAmount($priceInCents);
    }

    /**
     * Get price for currency using hybrid logic (EPIC 8 TICKET 8.3)
     * 
     * Implements the hybrid pricing model:
     * 1. Check for price override for the requested currency
     * 2. If no override, convert base price using exchange rate
     * 3. Return Money object for precise calculations
     * 
     * @param string $currencyCode ISO 4217 currency code
     * @return Money
     * @throws \InvalidArgumentException if currency is not found
     */
    public function getPriceForCurrency(string $currencyCode): Money
    {
        $currencyCode = strtoupper($currencyCode);
        
        // Step 1: Check for price override first
        $override = ProductPriceOverride::getEffectiveOverride($this->id, $currencyCode);
        if ($override) {
            return Money::ofMinor($override->price_cents, $currencyCode);
        }

        // Step 2: If requesting the same currency as base, return base price
        if ($currencyCode === $this->base_currency_code) {
            return Money::ofMinor($this->base_price_cents, $this->base_currency_code);
        }

        // Step 3: Convert from base currency using exchange rate
        $targetCurrency = Currency::where('code', $currencyCode)->first();
        if (!$targetCurrency) {
            throw new \InvalidArgumentException("Currency {$currencyCode} not found");
        }

        // Create base price Money object
        $basePrice = Money::ofMinor($this->base_price_cents, $this->base_currency_code);
        
        // Convert using the exchange rate
        $convertedPrice = $basePrice->convertTo($currencyCode, $targetCurrency->exchange_rate);
        
        return $convertedPrice;
    }

    /**
     * Check if product has a price override for a specific currency
     */
    public function hasPriceOverrideFor(string $currencyCode): bool
    {
        return ProductPriceOverride::getEffectiveOverride($this->id, $currencyCode) !== null;
    }

    /**
     * Set price override for a specific currency
     */
    public function setPriceOverride(string $currencyCode, int $priceCents, ?string $notes = null): ProductPriceOverride
    {
        return ProductPriceOverride::setOverride(
            $this->id,
            $currencyCode,
            $priceCents,
            notes: $notes
        );
    }

    /**
     * Remove price override for a specific currency
     */
    public function removePriceOverride(string $currencyCode): void
    {
        ProductPriceOverride::forProduct($this->id)
            ->forCurrency($currencyCode)
            ->delete();
    }

    /**
     * Legacy price accessor for backward compatibility
     * Returns base price as decimal
     */
    public function getPriceAttribute(): float
    {
        return $this->getBasePriceAsDecimal();
    }

    /**
     * Legacy price mutator for backward compatibility
     * Sets base price from decimal
     */
    public function setPriceAttribute(float $value): void
    {
        $this->setBasePriceFromDecimal($value);
    }
} 