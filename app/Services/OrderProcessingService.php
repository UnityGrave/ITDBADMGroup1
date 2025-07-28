<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Currency;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderProcessingService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create an order from checkout data
     *
     * @param User $user
     * @param array $checkoutData
     * @return Order
     * @throws Exception
     */
    public function createOrder(User $user, array $checkoutData): Order
    {
        return DB::transaction(function () use ($user, $checkoutData) {
            try {
                // Get cart items with product relationships loaded
                $cartItems = $this->cartService->getCartItems();
                
                if ($cartItems->isEmpty()) {
                    throw new Exception('Cannot create order: Cart is empty');
                }

                // Ensure we have the necessary product relationships loaded
                // The CartService already loads product.card, so we just need to ensure products are available
                foreach ($cartItems as $cartItem) {
                    if (!$cartItem->product) {
                        throw new Exception('Cart item missing product information');
                    }
                }

                // Get the current active currency for this transaction
                $activeCurrency = Currency::getActiveCurrencyObject();
                if (!$activeCurrency) {
                    throw new Exception('No active currency found for transaction');
                }

                // Get the base currency for conversion calculations
                $baseCurrency = Currency::getBaseCurrency();
                if (!$baseCurrency) {
                    throw new Exception('No base currency configured');
                }

                // Calculate totals in the active currency
                $subtotal = $this->cartService->getTotalPrice();
                $taxAmount = $subtotal * ($checkoutData['tax_rate'] ?? 0.08);
                $shippingCost = $checkoutData['shipping_cost'] ?? 9.99;
                $totalAmount = $subtotal + $taxAmount + $shippingCost;

                // Convert total to base currency for financial integrity
                // Store amounts in cents for precision
                $totalAmountInCents = (int)($totalAmount * 100);
                $totalInBaseCurrency = $activeCurrency->convertToBase($totalAmountInCents);

                // Create the order with currency information locked in
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'payment_method' => $checkoutData['payment_method'] ?? 'cod',
                    'payment_status' => 'pending',
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'currency_code' => $activeCurrency->code,
                    'exchange_rate' => $activeCurrency->exchange_rate,
                    'total_in_base_currency' => $totalInBaseCurrency,
                    'shipping_first_name' => $checkoutData['shipping_first_name'],
                    'shipping_last_name' => $checkoutData['shipping_last_name'],
                    'shipping_email' => $checkoutData['shipping_email'],
                    'shipping_phone' => $checkoutData['shipping_phone'],
                    'shipping_address_line_1' => $checkoutData['shipping_address_line_1'],
                    'shipping_address_line_2' => $checkoutData['shipping_address_line_2'] ?? null,
                    'shipping_city' => $checkoutData['shipping_city'],
                    'shipping_state' => $checkoutData['shipping_state'],
                    'shipping_postal_code' => $checkoutData['shipping_postal_code'],
                    'shipping_country' => $checkoutData['shipping_country'] ?? 'US',
                    'special_instructions' => $checkoutData['special_instructions'] ?? null,
                ]);

                // Create order items with currency conversion
                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->product;
                    
                    // Convert unit price to base currency for financial integrity
                    $unitPriceInCents = (int)($product->price * 100);
                    $priceInBaseCurrency = $activeCurrency->convertToBase($unitPriceInCents);
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->card->name ?? 'Unknown Product',
                        'product_sku' => $product->sku,
                        'unit_price' => $product->price,
                        'quantity' => $cartItem->quantity,
                        'price_in_base_currency' => $priceInBaseCurrency,
                        'product_details' => [
                            'category' => $product->card->category->name ?? null,
                            'condition' => $product->condition->value ?? null,
                            'set' => $product->card->set->name ?? null,
                            'rarity' => $product->card->rarity->name ?? null,
                            'collector_number' => $product->card->collector_number ?? null,
                        ],
                    ]);
                }

                // Clear the user's cart
                $this->cartService->clear();

                // Log successful order creation with currency information
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'currency_code' => $activeCurrency->code,
                    'exchange_rate' => $activeCurrency->exchange_rate,
                    'total_in_base_currency' => $totalInBaseCurrency,
                    'item_count' => $cartItems->count(),
                ]);

                return $order;

            } catch (Exception $e) {
                // Log the error
                Log::error('Order creation failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Re-throw the exception to trigger transaction rollback
                throw $e;
            }
        });
    }

    /**
     * Cancel an order (if possible)
     *
     * @param Order $order
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function cancelOrder(Order $order, string $reason = ''): bool
    {
        return DB::transaction(function () use ($order, $reason) {
            if (!$order->canBeCancelled()) {
                throw new Exception('Order cannot be cancelled at this stage');
            }

            $order->update([
                'status' => 'cancelled',
                'notes' => $order->notes . "\n" . "Cancelled: " . $reason,
            ]);

            Log::info('Order cancelled', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $reason,
            ]);

            return true;
        });
    }

    /**
     * Update order status
     *
     * @param Order $order
     * @param string $status
     * @param string $notes
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $status, string $notes = ''): bool
    {
        return DB::transaction(function () use ($order, $status, $notes) {
            $oldStatus = $order->status;
            
            $updateData = ['status' => $status];
            
            if ($notes) {
                $updateData['notes'] = $order->notes . "\n" . $notes;
            }

            // Set timestamps for specific statuses
            if ($status === 'shipped' && $oldStatus !== 'shipped') {
                $updateData['shipped_at'] = now();
            } elseif ($status === 'delivered' && $oldStatus !== 'delivered') {
                $updateData['delivered_at'] = now();
            }

            $order->update($updateData);

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'notes' => $notes,
            ]);

            return true;
        });
    }

    /**
     * Get order statistics for a user
     *
     * @param User $user
     * @return array
     */
    public function getUserOrderStats(User $user): array
    {
        $orders = $user->orders();

        return [
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->byStatus('pending')->count(),
            'completed_orders' => $orders->byStatus('delivered')->count(),
            'total_spent' => $orders->sum('total_amount'),
            'recent_orders' => $orders->recent(30)->count(),
        ];
    }

    /**
     * Validate checkout data
     *
     * @param array $checkoutData
     * @return bool
     * @throws Exception
     */
    public function validateCheckoutData(array $checkoutData): bool
    {
        $required = [
            'shipping_first_name',
            'shipping_last_name',
            'shipping_email',
            'shipping_phone',
            'shipping_address_line_1',
            'shipping_city',
            'shipping_state',
            'shipping_postal_code',
            'payment_method',
        ];

        foreach ($required as $field) {
            if (empty($checkoutData[$field])) {
                throw new Exception("Required field missing: {$field}");
            }
        }

        if (!in_array($checkoutData['payment_method'], ['cod', 'credit_card', 'paypal'])) {
            throw new Exception('Invalid payment method');
        }

        return true;
    }
}
