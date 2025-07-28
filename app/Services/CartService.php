<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_CART_KEY = 'cart';

    /**
     * Add a product to the cart.
     */
    public function add(Product $product, int $quantity = 1): void
    {
        if (Auth::check()) {
            $this->addToDatabase($product, $quantity);
        } else {
            $this->addToSession($product, $quantity);
        }
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function update(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        if (Auth::check()) {
            $this->updateInDatabase($productId, $quantity);
        } else {
            $this->updateInSession($productId, $quantity);
        }
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(int $productId): void
    {
        if (Auth::check()) {
            $this->removeFromDatabase($productId);
        } else {
            $this->removeFromSession($productId);
        }
    }

    /**
     * Get all cart items.
     */
    public function getCartItems(): Collection
    {
        if (Auth::check()) {
            return $this->getCartItemsFromDatabase();
        } else {
            return $this->getCartItemsFromSession();
        }
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): void
    {
        if (Auth::check()) {
            $this->clearDatabase();
        } else {
            $this->clearSession();
        }
    }

    /**
     * Get the total number of items in the cart.
     */
    public function getItemCount(): int
    {
        return $this->getCartItems()->sum('quantity');
    }

    /**
     * Get the total price of all items in the cart.
     */
    public function getTotalPrice(): float
    {
        return $this->getCartItems()->sum(function ($item) {
            // Use the current active currency price
            $activeCurrency = \App\Models\Currency::getActiveCurrencyObject();
            $priceObject = $item->product->getPriceForCurrency($activeCurrency->code ?? 'USD');
            return $priceObject->getAmountAsDecimal() * $item->quantity;
        });
    }

    /**
     * Get the total price of all items in the cart in USD (base currency).
     */
    public function getUsdTotalPrice(): float
    {
        return $this->getCartItems()->sum(function ($item) {
            $usdPrice = $item->product->getPriceForCurrency('USD');
            return $usdPrice->getAmountAsDecimal() * $item->quantity;
        });
    }

    /**
     * Migrate cart items from session to database when user logs in.
     */
    public function migrateSessionCartToDatabase(): void
    {
        if (!Auth::check()) {
            return;
        }

        $sessionCart = Session::get(self::SESSION_CART_KEY, []);
        
        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $this->addToDatabase($product, $quantity);
            }
        }

        // Clear session cart after migration
        Session::forget(self::SESSION_CART_KEY);
    }

    /**
     * Add product to database cart.
     */
    private function addToDatabase(Product $product, int $quantity): void
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * Add product to session cart.
     */
    private function addToSession(Product $product, int $quantity): void
    {
        $cart = Session::get(self::SESSION_CART_KEY, []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id] += $quantity;
        } else {
            $cart[$product->id] = $quantity;
        }

        Session::put(self::SESSION_CART_KEY, $cart);
    }

    /**
     * Update product quantity in database cart.
     */
    private function updateInDatabase(int $productId, int $quantity): void
    {
        CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->update(['quantity' => $quantity]);
    }

    /**
     * Update product quantity in session cart.
     */
    private function updateInSession(int $productId, int $quantity): void
    {
        $cart = Session::get(self::SESSION_CART_KEY, []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] = $quantity;
            Session::put(self::SESSION_CART_KEY, $cart);
        }
    }

    /**
     * Remove product from database cart.
     */
    private function removeFromDatabase(int $productId): void
    {
        CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Remove product from session cart.
     */
    private function removeFromSession(int $productId): void
    {
        $cart = Session::get(self::SESSION_CART_KEY, []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put(self::SESSION_CART_KEY, $cart);
        }
    }

    /**
     * Get cart items from database.
     */
    private function getCartItemsFromDatabase(): Collection
    {
        return CartItem::with(['product.card'])
            ->where('user_id', Auth::id())
            ->get();
    }

    /**
     * Get cart items from session.
     */
    private function getCartItemsFromSession(): Collection
    {
        $cart = Session::get(self::SESSION_CART_KEY, []);
        $cartItems = collect();

        foreach ($cart as $productId => $quantity) {
            $product = Product::with('card')->find($productId);
            if ($product) {
                // Create a temporary cart item object for consistency
                $cartItem = new CartItem([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
                $cartItem->setRelation('product', $product);
                $cartItems->push($cartItem);
            }
        }

        return $cartItems;
    }

    /**
     * Clear database cart.
     */
    private function clearDatabase(): void
    {
        CartItem::where('user_id', Auth::id())->delete();
    }

    /**
     * Clear session cart.
     */
    private function clearSession(): void
    {
        Session::forget(self::SESSION_CART_KEY);
    }
} 