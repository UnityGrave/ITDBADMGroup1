<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Currency;
use App\ValueObjects\Money;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductCard extends Component
{
    public Product $product;
    public $quantity = 1;
    public $currency;

    public function mount(Product $product, $currency = null)
    {
        $this->product = $product;
        $this->currency = $currency ?: Currency::getActiveCurrency();
    }

    /**
     * Add product to cart and dispatch event
     */
    public function addToCart()
    {
        // Dispatch event that will be caught by ShoppingCart component
        $this->dispatch('product-added-to-cart', 
            productId: $this->product->id, 
            quantity: $this->quantity
        );
    }

    /**
     * Quick add to cart with quantity 1
     */
    public function quickAddToCart()
    {
        $this->quantity = 1;
        $this->addToCart();
    }

    /**
     * Update quantity
     */
    public function updateQuantity($newQuantity)
    {
        $this->quantity = max(1, intval($newQuantity));
    }

    /**
     * Get price in selected currency using hybrid pricing logic
     */
    public function getPriceInCurrency(): Money
    {
        return $this->product->getPriceForCurrency($this->currency);
    }

    /**
     * Get formatted price string for display with caching
     */
    public function getFormattedPriceProperty(): string
    {
        $cacheKey = "product_price_{$this->product->id}_{$this->currency}";
        
        return cache()->remember($cacheKey, 300, function () {
            return $this->getPriceInCurrency()->format();
        });
    }

    /**
     * Change currency
     */
    public function setCurrency($currencyCode)
    {
        $this->currency = strtoupper($currencyCode);
    }

    /**
     * Listen for global currency change events
     */
    #[On('currency-changed')]
    public function handleCurrencyChange($currency)
    {
        $this->currency = $currency;
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
