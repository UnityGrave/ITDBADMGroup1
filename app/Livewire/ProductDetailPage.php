<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Currency;
use App\Services\CartService;
use App\ValueObjects\Money;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductDetailPage extends Component
{
    public Product $product;
    public $quantity = 1;
    public $currency;
    
    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount(Product $product, $currency = null)
    {
        $this->product = $product->load('card.set', 'card.rarity', 'card.category', 'inventory');
        $this->currency = $currency ?: Currency::getActiveCurrency();
    }

    /**
     * Add product to cart
     */
    public function addToCart()
    {
        if ($this->product->inventory->stock === 0) {
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('This item is out of stock!', 'error');
                }
            ");
            return;
        }

        $this->cartService->add($this->product, $this->quantity);
        
        // Dispatch event to update cart in navigation
        $this->dispatch('product-added-to-cart', 
            productId: $this->product->id, 
            quantity: $this->quantity
        );

        // Show success feedback
        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Added {$this->quantity} item(s) to cart!', 'success');
            }
        ");
    }

    /**
     * Update quantity
     */
    public function updateQuantity($newQuantity)
    {
        $this->quantity = max(1, min($newQuantity, $this->product->inventory->stock));
    }

    /**
     * Increment quantity
     */
    public function incrementQuantity()
    {
        if ($this->quantity < $this->product->inventory->stock) {
            $this->quantity++;
        }
    }

    /**
     * Decrement quantity
     */
    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Get price in selected currency using hybrid pricing logic
     */
    public function getPriceInCurrency(): Money
    {
        return $this->product->getPriceForCurrency($this->currency);
    }

    /**
     * Get formatted price string for display
     */
    public function getFormattedPriceProperty(): string
    {
        return $this->getPriceInCurrency()->format();
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
        return view('livewire.product-detail-page')->layout('layouts.app');
    }
} 