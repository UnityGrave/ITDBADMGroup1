<?php

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Money value object for precise monetary calculations
 * 
 * This is a simplified implementation while we await brick/money installation.
 * Stores amounts as integers (smallest currency unit) to avoid floating-point errors.
 */
class Money implements JsonSerializable
{
    private int $amount;
    private string $currency;
    
    /**
     * @param int $amount Amount in smallest currency unit (e.g., cents)
     * @param string $currency ISO 4217 currency code
     */
    public function __construct(int $amount, string $currency)
    {
        if (empty($currency) || strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a valid 3-letter ISO code');
        }
        
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }
    
    /**
     * Create Money from major currency unit (e.g., dollars)
     */
    public static function of(float|string $amount, string $currency): self
    {
        // Convert to smallest unit (multiply by 100 for most currencies)
        $decimalPlaces = self::getDecimalPlaces($currency);
        $multiplier = pow(10, $decimalPlaces);
        
        if (is_string($amount)) {
            // Use bcmul for precise string arithmetic
            $cents = (int) bcmul($amount, (string) $multiplier, 0);
        } else {
            $cents = (int) round($amount * $multiplier);
        }
        
        return new self($cents, $currency);
    }
    
    /**
     * Create Money from smallest currency unit (e.g., cents)
     */
    public static function ofMinor(int $amount, string $currency): self
    {
        return new self($amount, $currency);
    }
    
    /**
     * Get amount in smallest currency unit
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
    
    /**
     * Get currency code
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    /**
     * Get amount in major currency unit (e.g., dollars)
     */
    public function getAmountAsDecimal(): string
    {
        $decimalPlaces = self::getDecimalPlaces($this->currency);
        $divisor = pow(10, $decimalPlaces);
        
        return bcdiv((string) $this->amount, (string) $divisor, $decimalPlaces);
    }
    
    /**
     * Convert to another currency using exchange rate
     */
    public function convertTo(string $targetCurrency, float $exchangeRate): self
    {
        if ($this->currency === $targetCurrency) {
            return $this;
        }
        
        // Convert using precise arithmetic
        $rateString = number_format($exchangeRate, 8, '.', '');
        $convertedAmount = bcmul((string) $this->amount, $rateString, 0);
        
        return new self((int) $convertedAmount, $targetCurrency);
    }
    
    /**
     * Add another money amount (must be same currency)
     */
    public function plus(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }
    
    /**
     * Subtract another money amount (must be same currency)
     */
    public function minus(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }
    
    /**
     * Multiply by a factor
     */
    public function multipliedBy(float $factor): self
    {
        $factorString = number_format($factor, 8, '.', '');
        $newAmount = bcmul((string) $this->amount, $factorString, 0);
        
        return new self((int) $newAmount, $this->currency);
    }
    
    /**
     * Check if amounts are equal
     */
    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
    
    /**
     * Compare with another money amount
     */
    public function compareTo(Money $other): int
    {
        $this->assertSameCurrency($other);
        return $this->amount <=> $other->amount;
    }
    
    /**
     * Check if this amount is greater than another
     */
    public function isGreaterThan(Money $other): bool
    {
        return $this->compareTo($other) > 0;
    }
    
    /**
     * Check if this amount is zero
     */
    public function isZero(): bool
    {
        return $this->amount === 0;
    }
    
    /**
     * Check if this amount is positive
     */
    public function isPositive(): bool
    {
        return $this->amount > 0;
    }
    
    /**
     * Format for display using Laravel's Number helper
     */
    public function format(): string
    {
        // Try Laravel's Number helper first (requires intl extension)
        if (function_exists('number_format') && class_exists('\Illuminate\Support\Number')) {
            try {
                return \Illuminate\Support\Number::currency(
                    $this->getAmountAsDecimal(),
                    $this->currency
                );
            } catch (\Exception $e) {
                // Fall through to manual formatting if intl extension is missing
            }
        }
        
        // Fallback formatting
        return $this->getSymbol() . number_format(
            (float) $this->getAmountAsDecimal(),
            $this->getDecimalPlaces($this->currency),
            '.',
            ','
        );
    }
    
    /**
     * Get currency symbol
     */
    public function getSymbol(): string
    {
        return match ($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF ',
            'SEK' => 'kr',
            'PHP' => '₱',
            default => $this->currency . ' ',
        };
    }
    
    /**
     * Get decimal places for currency
     */
    private static function getDecimalPlaces(string $currency): int
    {
        return match ($currency) {
            'JPY', 'KRW' => 0, // These currencies don't use decimal places
            default => 2,
        };
    }
    
    /**
     * Assert that two money objects have the same currency
     */
    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot perform operation on different currencies: {$this->currency} and {$other->currency}"
            );
        }
    }
    
    /**
     * Convert to string
     */
    public function __toString(): string
    {
        return $this->format();
    }
    
    /**
     * JSON serialization
     */
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted' => $this->format(),
            'decimal' => $this->getAmountAsDecimal(),
        ];
    }
    
    /**
     * Create from JSON data
     */
    public static function fromJson(array $data): self
    {
        return new self($data['amount'], $data['currency']);
    }
} 