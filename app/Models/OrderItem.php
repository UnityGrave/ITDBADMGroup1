<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'total_price',
        'price_in_base_currency',
        'product_details',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
        'price_in_base_currency' => 'integer',
        'product_details' => 'array',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with the order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
        });
    }

    /**
     * Get the formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted total price
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        if ($this->order && $this->order->currency) {
            return $this->order->currency->formatAmount((int)($this->total_price * 100));
        }
        return '$' . number_format($this->total_price, 2);
    }

    /**
     * Get the formatted unit price in order currency
     */
    public function getFormattedUnitPriceInOrderCurrencyAttribute(): string
    {
        if ($this->order && $this->order->currency) {
            return $this->order->currency->formatAmount((int)($this->unit_price * 100));
        }
        return '$' . number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted price in base currency for reporting
     */
    public function getFormattedBasePriceAttribute(): string
    {
        $baseCurrency = \App\Models\Currency::getBaseCurrency();
        if ($baseCurrency) {
            return $baseCurrency->formatAmount($this->price_in_base_currency);
        }
        return '$' . number_format($this->price_in_base_currency / 100, 2);
    }

    /**
     * Get the total price in base currency
     */
    public function getTotalPriceInBaseCurrencyAttribute(): int
    {
        return $this->price_in_base_currency * $this->quantity;
    }
}
