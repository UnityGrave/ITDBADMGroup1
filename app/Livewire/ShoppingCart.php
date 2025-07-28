<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class ShoppingCart extends Component
{
    public $isOpen = false;
    public $cartItems = [];
    public $cartCount = 0;
    public $cartTotal = 0.00;

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->refreshCart();
    }

    /**
     * Listen for add-to-cart events from other components
     */
    #[On('product-added-to-cart')]
    public function handleProductAdded($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->cartService->add($product, $quantity);
            $this->refreshCart();
            
            // Show success message
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('Product added to cart!', 'success');
                }
            ");
        }
    }

    /**
     * Listen for add-to-cart events from Blade components (Alpine.js)
     */
    #[On('add-to-cart')]
    public function handleAddToCart($productId, $quantity = 1)
    {
        $this->handleProductAdded($productId, $quantity);
    }

    /**
     * Update quantity of a cart item
     */
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $this->cartService->update($productId, $quantity);
        $this->refreshCart();
    }

    /**
     * Remove an item from the cart
     */
    public function removeItem($productId)
    {
        $this->cartService->remove($productId);
        $this->refreshCart();
        
        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Item removed from cart', 'info');
            }
        ");
    }

    /**
     * Clear all items from the cart
     */
    public function clearCart()
    {
        $this->cartService->clear();
        $this->refreshCart();
        
        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Cart cleared', 'info');
            }
        ");
    }

    /**
     * Toggle cart visibility
     */
    public function toggleCart()
    {
        $this->isOpen = !$this->isOpen;
    }

    /**
     * Open the cart
     */
    public function openCart()
    {
        $this->isOpen = true;
    }

    /**
     * Close the cart
     */
    public function closeCart()
    {
        $this->isOpen = false;
    }

    /**
     * Refresh cart data from the service
     */
    public function refreshCart()
    {
        $items = $this->cartService->getCartItems();
        
        // Convert to array but ensure product is a model object
        $this->cartItems = $items->map(function ($item) {
            $itemArray = $item->toArray();
            // Ensure product is a model object, not an array
            if (isset($itemArray['product']) && is_array($itemArray['product'])) {
                $itemArray['product'] = $item->product;
            }
            return $itemArray;
        })->toArray();
        
        $this->cartCount = $this->cartService->getItemCount();
        $this->cartTotal = $this->cartService->getTotalPrice();
    }

    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        if ($this->cartCount === 0) {
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('Your cart is empty', 'warning');
                }
            ");
            return;
        }

        // Redirect to checkout page
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
