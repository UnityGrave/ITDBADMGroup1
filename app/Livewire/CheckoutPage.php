<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Services\OrderProcessingService;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutPage extends Component
{
    // Checkout step control
    public string $step = 'shipping';
    
    // Cart data
    public $cartItems = [];
    public $cartCount = 0;
    public $cartTotal = 0.00;
    public $shippingCost = 9.99;
    public $taxRate = 0.08; // 8% tax
    public $taxAmount = 0.00;
    public $finalTotal = 0.00;
    
    // Currency data
    public $activeCurrency = null;
    public $currencySymbol = '$';

    // Shipping information (using defer for performance)
    public string $shipping_first_name = '';
    public string $shipping_last_name = '';
    public string $shipping_email = '';
    public string $shipping_phone = '';
    public string $shipping_address_line_1 = '';
    public string $shipping_address_line_2 = '';
    public string $shipping_city = '';
    public string $shipping_state = '';
    public string $shipping_postal_code = '';
    public string $shipping_country = 'US';

    // Payment information
    public string $payment_method = 'cod'; // Cash on Delivery
    public string $special_instructions = '';

    protected $cartService;
    protected $orderProcessingService;

    public function boot(CartService $cartService, OrderProcessingService $orderProcessingService)
    {
        $this->cartService = $cartService;
        $this->orderProcessingService = $orderProcessingService;
    }

    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Load currency information
        $this->loadCurrencyData();
        
        // Load cart data
        $this->refreshCart();

        // If cart is empty, redirect to products page
        if ($this->cartCount === 0) {
            session()->flash('error', 'Your cart is empty. Please add items before checkout.');
            return redirect()->route('products.index');
        }

        // Pre-fill user information if available
        $user = Auth::user();
        $this->shipping_first_name = explode(' ', $user->name)[0] ?? '';
        $this->shipping_last_name = explode(' ', $user->name, 2)[1] ?? '';
        $this->shipping_email = $user->email;
    }

    /**
     * Validation rules for different steps
     */
    protected function rules(): array
    {
        // Base rules for all steps (used in final validation)
        $allRules = [
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|size:2',
            'payment_method' => 'required|in:cod',
            'special_instructions' => 'nullable|string|max:1000',
        ];

        // Return step-specific rules for individual step validation
        if ($this->step === 'shipping') {
            return [
                'shipping_first_name' => 'required|string|max:255',
                'shipping_last_name' => 'required|string|max:255',
                'shipping_email' => 'required|email|max:255',
                'shipping_phone' => 'required|string|max:20',
                'shipping_address_line_1' => 'required|string|max:255',
                'shipping_address_line_2' => 'nullable|string|max:255',
                'shipping_city' => 'required|string|max:255',
                'shipping_state' => 'required|string|max:255',
                'shipping_postal_code' => 'required|string|max:20',
                'shipping_country' => 'required|string|size:2',
            ];
        }

        if ($this->step === 'payment') {
            return [
                'payment_method' => 'required|in:cod',
                'special_instructions' => 'nullable|string|max:1000',
            ];
        }

        // Return all rules for final validation (placeOrder)
        return $allRules;
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'shipping_first_name.required' => 'First name is required.',
            'shipping_last_name.required' => 'Last name is required.',
            'shipping_email.required' => 'Email address is required.',
            'shipping_email.email' => 'Please enter a valid email address.',
            'shipping_phone.required' => 'Phone number is required.',
            'shipping_address_line_1.required' => 'Street address is required.',
            'shipping_city.required' => 'City is required.',
            'shipping_state.required' => 'State is required.',
            'shipping_postal_code.required' => 'Postal code is required.',
            'shipping_country.required' => 'Country is required.',
            'payment_method.required' => 'Please select a payment method.',
        ];
    }

    /**
     * Move to the next step
     */
    public function nextStep()
    {
        $this->validate();

        switch ($this->step) {
            case 'shipping':
                $this->step = 'payment';
                break;
            case 'payment':
                $this->step = 'review';
                $this->calculateTotals();
                break;
        }
    }

    /**
     * Move to the previous step
     */
    public function previousStep()
    {
        switch ($this->step) {
            case 'payment':
                $this->step = 'shipping';
                break;
            case 'review':
                $this->step = 'payment';
                break;
        }
    }

    /**
     * Go to a specific step
     */
    public function goToStep(string $step)
    {
        if (in_array($step, ['shipping', 'payment', 'review'])) {
            $this->step = $step;
            if ($step === 'review') {
                $this->calculateTotals();
            }
        }
    }

    /**
     * Update item quantity in cart
     */
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->cartService->remove($productId);
        } else {
            $this->cartService->update($productId, $quantity);
        }
        
        $this->refreshCart();

        // If cart becomes empty, redirect
        if ($this->cartCount === 0) {
            session()->flash('error', 'Your cart is now empty.');
            return redirect()->route('products.index');
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($productId)
    {
        $this->cartService->remove($productId);
        $this->refreshCart();

        // If cart becomes empty, redirect
        if ($this->cartCount === 0) {
            session()->flash('error', 'Your cart is now empty.');
            return redirect()->route('products.index');
        }

        $this->js("
            if (typeof window.showToast === 'function') {
                window.showToast('Item removed from cart', 'info');
            }
        ");
    }

    /**
     * Listen for currency change events
     */
    #[On('currency-changed')]
    public function handleCurrencyChanged($currency)
    {
        // Reload currency data and refresh cart
        $this->loadCurrencyData();
        $this->refreshCart();
        $this->calculateTotals();
    }

    /**
     * Process the order using OrderProcessingService
     */
    public function placeOrder()
    {
        // Validate all steps
        $this->validate();

        if ($this->cartCount === 0) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        try {
            // Prepare checkout data
            $checkoutData = [
                'shipping_first_name' => $this->shipping_first_name,
                'shipping_last_name' => $this->shipping_last_name,
                'shipping_email' => $this->shipping_email,
                'shipping_phone' => $this->shipping_phone,
                'shipping_address_line_1' => $this->shipping_address_line_1,
                'shipping_address_line_2' => $this->shipping_address_line_2,
                'shipping_city' => $this->shipping_city,
                'shipping_state' => $this->shipping_state,
                'shipping_postal_code' => $this->shipping_postal_code,
                'shipping_country' => $this->shipping_country,
                'payment_method' => $this->payment_method,
                'special_instructions' => $this->special_instructions,
                'tax_rate' => $this->taxRate,
                'shipping_cost' => $this->shippingCost,
            ];

            // Create the order using the service (with database transaction)
            $order = $this->orderProcessingService->createOrder(Auth::user(), $checkoutData);

            // Show success message
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('Order placed successfully!', 'success');
                }
            ");

            // Redirect to order success page
            session()->flash('success', 'Your order has been placed successfully!');
            session()->flash('order_number', $order->order_number);
            
            return redirect()->route('order.success', ['order' => $order->order_number]);

        } catch (\App\Exceptions\OrderProcessingException $e) {
            // Handle known order processing errors
            $this->addError('general', 'There was an error processing your order. Please try again.');
            
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('Order failed due to a processing error. Please contact support if the issue persists.', 'error');
                }
            ");

            \Log::error('Order processing error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'checkout_data' => $checkoutData,
            ]);
        } catch (\Throwable $e) {
            // Handle unexpected errors
            $this->addError('general', 'An unexpected error occurred. Please try again later.');
            
            $this->js("
                if (typeof window.showToast === 'function') {
                    window.showToast('An unexpected error occurred. Please try again later.', 'error');
                }
            ");

            \Log::error('Unexpected error during order placement', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'checkout_data' => $checkoutData,
            ]);
        }
    }

    /**
     * Refresh cart data from service
     */
    private function refreshCart()
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
     * Load currency data for the checkout
     */
    private function loadCurrencyData()
    {
        $this->activeCurrency = Currency::getActiveCurrencyObject();
        if ($this->activeCurrency) {
            $this->currencySymbol = $this->activeCurrency->symbol;
        }
    }

    /**
     * Calculate totals including tax and shipping
     */
    private function calculateTotals()
    {
        $this->taxAmount = $this->cartTotal * $this->taxRate;
        $this->finalTotal = $this->cartTotal + $this->shippingCost + $this->taxAmount;
    }

    /**
     * Get formatted amount in active currency
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
     * Get currency display information for the UI
     */
    public function getCurrencyDisplayInfo(): array
    {
        if (!$this->activeCurrency) {
            return [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'US Dollar'
            ];
        }

        return [
            'code' => $this->activeCurrency->code,
            'symbol' => $this->activeCurrency->symbol,
            'name' => $this->activeCurrency->name
        ];
    }

    /**
     * Get progress percentage for current step
     */
    public function getProgressPercentage(): int
    {
        return match ($this->step) {
            'shipping' => 33,
            'payment' => 66,
            'review' => 100,
            default => 0,
        };
    }

    /**
     * Check if a step is completed
     */
    public function isStepCompleted(string $stepName): bool
    {
        $stepOrder = ['shipping' => 1, 'payment' => 2, 'review' => 3];
        $currentStepOrder = $stepOrder[$this->step] ?? 0;
        $checkStepOrder = $stepOrder[$stepName] ?? 0;

        return $checkStepOrder < $currentStepOrder;
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
