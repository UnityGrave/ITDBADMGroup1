<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product;
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

        // Show success feedback
        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Added to cart!', 'success');
            }
        ");
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

    public function render()
    {
        return view('livewire.product-card');
    }
}
