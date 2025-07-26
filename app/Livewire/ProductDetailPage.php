<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class ProductDetailPage extends Component
{
    public Product $product;
    public $quantity = 1;
    
    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount(Product $product)
    {
        $this->product = $product->load('card.set', 'card.rarity', 'card.category', 'inventory');
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

    public function render()
    {
        return view('livewire.product-detail-page')->layout('layouts.app');
    }
} 