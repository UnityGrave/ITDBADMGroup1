<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Models\Product;
use App\Models\Currency;
use App\ValueObjects\Money;
use Livewire\Component;
use Livewire\Attributes\On;

class ShoppingCart extends Component
{
    public $isOpen = false;
    public $cartItems = [];
    public $cartCount = 0;
    public $cartTotal = 0.00;
    public $activeCurrency = null;

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->loadActiveCurrency();
        $this->refreshCart();
    }

    /**
     * Load the active currency for price formatting
     */
    public function loadActiveCurrency()
    {
        $this->activeCurrency = Currency::getActiveCurrencyObject();
    }

    /**
     * Listen for add-to-cart events from other components
     */
    #[On('product-added-to-cart')]
    public function handleProductAdded($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        if ($product) {
            try {
                $this->cartService->add($product, $quantity);
                $this->refreshCart();
                
                // Show success message
                $this->js("
                    if (typeof window.showToast === 'function') {
                        window.showToast('Product added to cart!', 'success');
                    }
                ");
            } catch (\Exception $e) {
                // Handle stock validation errors
                $this->js("
                    if (typeof window.showToast === 'function') {
                        window.showToast('{$e->getMessage()}', 'error');
                    }
                ");
            }
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

        try {
            $this->cartService->update($productId, $quantity);
            $this->refreshCart();
        } catch (\Exception $e) {
            // Handle stock validation errors
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('{$e->getMessage()}', 'error');
                }
            ");
            // Refresh cart to show correct quantities
            $this->refreshCart();
        }
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
        $this->cartItems = $items->toArray();
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

    /**
     * Get price in USD for a product
     */
    public function getUsdPrice($product): Money
    {
        if (is_array($product)) {
            $productModel = Product::find($product['id']);
        } else {
            $productModel = $product;
        }
        
        return $productModel->getPriceForCurrency('USD');
    }

    /**
     * Get price in active currency for a product  
     */
    public function getActiveCurrencyPrice($product): Money
    {
        if (is_array($product)) {
            $productModel = Product::find($product['id']);
        } else {
            $productModel = $product;
        }
        
        $currencyCode = $this->activeCurrency ? $this->activeCurrency->code : 'USD';
        return $productModel->getPriceForCurrency($currencyCode);
    }

    /**
     * Format amount in active currency
     */
    public function formatAmount($amount): string
    {
        if ($this->activeCurrency) {
            return $this->activeCurrency->formatAmount((int)($amount * 100));
        }
        return '$' . number_format($amount, 2);
    }

    /**
     * Get the active currency code
     */
    public function getActiveCurrencyCode(): string
    {
        return $this->activeCurrency ? $this->activeCurrency->code : 'USD';
    }

    /**
     * Check if current currency is different from USD
     */
    public function isNonUsdCurrency(): bool
    {
        return $this->getActiveCurrencyCode() !== 'USD';
    }

    /**
     * Listen for currency changes
     */
    #[On('currency-changed')]
    public function handleCurrencyChange($currency)
    {
        $this->loadActiveCurrency();
        $this->refreshCart();
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
