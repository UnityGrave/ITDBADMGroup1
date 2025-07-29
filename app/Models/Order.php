<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'currency_code',
        'exchange_rate',
        'total_in_base_currency',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'special_instructions',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
        'total_in_base_currency' => 'integer',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Boot method to generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the currency for this order.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Get the full shipping address formatted
     */
    public function getFormattedShippingAddressAttribute(): string
    {
        $address = $this->shipping_address_line_1;
        
        if ($this->shipping_address_line_2) {
            $address .= ', ' . $this->shipping_address_line_2;
        }
        
        $address .= ', ' . $this->shipping_city . ', ' . $this->shipping_state . ' ' . $this->shipping_postal_code;
        
        return $address;
    }

    /**
     * Get the customer's full name
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->shipping_first_name . ' ' . $this->shipping_last_name;
    }

    /**
     * Check if the order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if the order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope to get orders by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent orders
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted total amount in the order's currency
     */
    public function getFormattedTotalAttribute(): string
    {
        if ($this->currency && $this->isNonUsdCurrency()) {
            // Convert from USD (stored amount) to display currency
            $usdAmountInCents = (int)($this->total_amount * 100);
            $convertedAmount = $this->currency->convertFromBase($usdAmountInCents);
            return $this->currency->formatAmount($convertedAmount);
        }
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted subtotal in the order's currency
     */
    public function getFormattedSubtotalAttribute(): string
    {
        if ($this->currency && $this->isNonUsdCurrency()) {
            // Convert from USD (stored amount) to display currency
            $usdAmountInCents = (int)($this->subtotal * 100);
            $convertedAmount = $this->currency->convertFromBase($usdAmountInCents);
            return $this->currency->formatAmount($convertedAmount);
        }
        return '$' . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted tax amount in the order's currency
     */
    public function getFormattedTaxAmountAttribute(): string
    {
        if ($this->currency && $this->isNonUsdCurrency()) {
            // Convert from USD (stored amount) to display currency
            $usdAmountInCents = (int)($this->tax_amount * 100);
            $convertedAmount = $this->currency->convertFromBase($usdAmountInCents);
            return $this->currency->formatAmount($convertedAmount);
        }
        return '$' . number_format($this->tax_amount, 2);
    }

    /**
     * Get formatted shipping cost in the order's currency
     */
    public function getFormattedShippingCostAttribute(): string
    {
        if ($this->currency && $this->isNonUsdCurrency()) {
            // Convert from USD (stored amount) to display currency
            $usdAmountInCents = (int)($this->shipping_cost * 100);
            $convertedAmount = $this->currency->convertFromBase($usdAmountInCents);
            return $this->currency->formatAmount($convertedAmount);
        }
        return '$' . number_format($this->shipping_cost, 2);
    }

    /**
     * Get formatted total in base currency for reporting
     */
    public function getFormattedBaseTotalAttribute(): string
    {
        $baseCurrency = Currency::getBaseCurrency();
        if ($baseCurrency) {
            return $baseCurrency->formatAmount($this->total_in_base_currency);
        }
        return '$' . number_format($this->total_in_base_currency / 100, 2);
    }

    /**
     * Check if the order is in a non-USD currency
     */
    public function isNonUsdCurrency(): bool
    {
        return $this->currency_code !== 'USD';
    }

    /**
     * Get USD amounts for the order (for dual currency display)
     */
    public function getUsdAmounts(): array
    {
        // Since we now store USD amounts directly, just return them
        return [
            'subtotal' => $this->subtotal,
            'shipping' => $this->shipping_cost,
            'tax' => $this->tax_amount,
            'total' => $this->total_amount,
        ];
    }

    /**
     * Get formatted USD amounts
     */
    public function getFormattedUsdSubtotalAttribute(): string
    {
        $usdAmounts = $this->getUsdAmounts();
        return '$' . number_format($usdAmounts['subtotal'], 2);
    }

    public function getFormattedUsdShippingAttribute(): string
    {
        $usdAmounts = $this->getUsdAmounts();
        return '$' . number_format($usdAmounts['shipping'], 2);
    }

    public function getFormattedUsdTaxAttribute(): string
    {
        $usdAmounts = $this->getUsdAmounts();
        return '$' . number_format($usdAmounts['tax'], 2);
    }

    public function getFormattedUsdTotalAttribute(): string
    {
        $usdAmounts = $this->getUsdAmounts();
        return '$' . number_format($usdAmounts['total'], 2);
    }
}
