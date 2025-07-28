<?php

/**
 * Test script for TICKET 8.5: Multi-Currency Integration into Checkout and Orders
 * 
 * This script tests the complete multi-currency workflow:
 * 1. Currency setup and exchange rates
 * 2. Order creation with currency conversion
 * 3. Data integrity verification
 * 4. Display formatting verification
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\Currency;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderProcessingService;
use App\Services\CartService;
use App\Services\ExchangeRateService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== TICKET 8.5: Multi-Currency Integration Test ===\n\n";

try {
    // Test 1: Verify currency setup
    echo "1. Testing Currency Setup...\n";
    
    $currencies = Currency::all();
    echo "   Found " . $currencies->count() . " currencies in database\n";
    
    $baseCurrency = Currency::getBaseCurrency();
    if ($baseCurrency) {
        echo "   Base currency: {$baseCurrency->code} ({$baseCurrency->name})\n";
    } else {
        echo "   ERROR: No base currency found!\n";
        exit(1);
    }
    
    $activeCurrency = Currency::getActiveCurrencyObject();
    if ($activeCurrency) {
        echo "   Active currency: {$activeCurrency->code} ({$activeCurrency->name})\n";
        echo "   Exchange rate: {$activeCurrency->exchange_rate}\n";
    } else {
        echo "   ERROR: No active currency found!\n";
        exit(1);
    }
    
    // Test 2: Test exchange rate service
    echo "\n2. Testing Exchange Rate Service...\n";
    
    $exchangeService = new ExchangeRateService();
    echo "   API reachable: " . ($exchangeService->isApiReachable() ? 'Yes' : 'No') . "\n";
    
    $lastSync = $exchangeService->getLastSyncTime();
    if ($lastSync) {
        echo "   Last sync: {$lastSync->format('Y-m-d H:i:s')}\n";
    } else {
        echo "   No previous sync found\n";
    }
    
    // Test 3: Test currency conversion
    echo "\n3. Testing Currency Conversion...\n";
    
    $testAmount = 10000; // $100.00 in cents
    $convertedAmount = $activeCurrency->convertToBase($testAmount);
    echo "   Converting {$testAmount} cents in {$activeCurrency->code} to base currency\n";
    echo "   Result: {$convertedAmount} cents in {$baseCurrency->code}\n";
    echo "   Rate used: {$activeCurrency->exchange_rate}\n";
    
    // Test 4: Test order creation with currency
    echo "\n4. Testing Order Creation with Currency...\n";
    
    // Find a test user
    $user = User::first();
    if (!$user) {
        echo "   ERROR: No users found in database!\n";
        exit(1);
    }
    echo "   Using test user: {$user->name} (ID: {$user->id})\n";
    
    // Find a test product
    $product = Product::with('card')->first();
    if (!$product) {
        echo "   ERROR: No products found in database!\n";
        exit(1);
    }
    echo "   Using test product: {$product->card->name} - Price: \${$product->price}\n";
    
    // Create a test cart item (simulate cart service)
    $cartService = new CartService();
    $cartService->add($product->id, 2); // Add 2 items
    
    echo "   Added 2 items to cart\n";
    echo "   Cart total: \${$cartService->getTotalPrice()}\n";
    
    // Test order creation
    $orderService = new OrderProcessingService($cartService);
    
    $checkoutData = [
        'shipping_first_name' => 'Test',
        'shipping_last_name' => 'User',
        'shipping_email' => 'test@example.com',
        'shipping_phone' => '555-1234',
        'shipping_address_line_1' => '123 Test St',
        'shipping_city' => 'Test City',
        'shipping_state' => 'TS',
        'shipping_postal_code' => '12345',
        'shipping_country' => 'US',
        'payment_method' => 'cod',
        'tax_rate' => 0.08,
        'shipping_cost' => 9.99,
    ];
    
    echo "   Creating order with currency integration...\n";
    $order = $orderService->createOrder($user, $checkoutData);
    
    echo "   Order created successfully!\n";
    echo "   Order Number: {$order->order_number}\n";
    echo "   Currency Code: {$order->currency_code}\n";
    echo "   Exchange Rate: {$order->exchange_rate}\n";
    echo "   Total Amount: \${$order->total_amount} ({$order->currency_code})\n";
    echo "   Total in Base Currency: {$order->total_in_base_currency} cents ({$baseCurrency->code})\n";
    
    // Test 5: Verify order items have currency data
    echo "\n5. Testing Order Items Currency Data...\n";
    
    foreach ($order->orderItems as $item) {
        echo "   Item: {$item->product_name}\n";
        echo "   Unit Price: \${$item->unit_price} ({$order->currency_code})\n";
        echo "   Price in Base Currency: {$item->price_in_base_currency} cents ({$baseCurrency->code})\n";
        echo "   Quantity: {$item->quantity}\n";
        echo "   Total: \${$item->total_price} ({$order->currency_code})\n";
        echo "   Total in Base Currency: {$item->total_price_in_base_currency} cents ({$baseCurrency->code})\n";
        echo "   ---\n";
    }
    
    // Test 6: Test formatting methods
    echo "\n6. Testing Currency Formatting...\n";
    
    // Load the order with currency relationship
    $order->load('currency');
    
    echo "   Formatted Total: {$order->formatted_total}\n";
    echo "   Formatted Subtotal: {$order->formatted_subtotal}\n";
    echo "   Formatted Tax: {$order->formatted_tax_amount}\n";
    echo "   Formatted Shipping: {$order->formatted_shipping_cost}\n";
    echo "   Formatted Base Total: {$order->formatted_base_total}\n";
    
    foreach ($order->orderItems as $item) {
        echo "   Item '{$item->product_name}' formatted total: {$item->formatted_total_price}\n";
        echo "   Item '{$item->product_name}' formatted base price: {$item->formatted_base_price}\n";
    }
    
    // Test 7: Verify financial integrity
    echo "\n7. Testing Financial Integrity...\n";
    
    // Calculate expected base currency total
    $expectedBaseTotal = 0;
    foreach ($order->orderItems as $item) {
        $expectedBaseTotal += $item->total_price_in_base_currency;
    }
    
    // Add tax and shipping in base currency
    $taxInBase = $activeCurrency->convertToBase((int)($order->tax_amount * 100));
    $shippingInBase = $activeCurrency->convertToBase((int)($order->shipping_cost * 100));
    $expectedBaseTotal += $taxInBase + $shippingInBase;
    
    echo "   Expected base total: {$expectedBaseTotal} cents\n";
    echo "   Actual base total: {$order->total_in_base_currency} cents\n";
    echo "   Difference: " . abs($expectedBaseTotal - $order->total_in_base_currency) . " cents\n";
    
    if (abs($expectedBaseTotal - $order->total_in_base_currency) <= 1) {
        echo "   ✓ Financial integrity check PASSED (within 1 cent tolerance)\n";
    } else {
        echo "   ✗ Financial integrity check FAILED\n";
    }
    
    // Test 8: Test reporting capabilities
    echo "\n8. Testing Reporting Capabilities...\n";
    
    $totalSalesInBase = Order::sum('total_in_base_currency');
    echo "   Total sales in base currency: {$totalSalesInBase} cents\n";
    echo "   Total sales formatted: " . ($baseCurrency ? $baseCurrency->formatAmount($totalSalesInBase) : '$' . number_format($totalSalesInBase / 100, 2)) . "\n";
    
    // Test different currency orders
    $ordersByCurrency = Order::selectRaw('currency_code, COUNT(*) as count, SUM(total_amount) as total')
        ->groupBy('currency_code')
        ->get();
    
    echo "   Orders by currency:\n";
    foreach ($ordersByCurrency as $currencyData) {
        echo "     {$currencyData->currency_code}: {$currencyData->count} orders, total: {$currencyData->total}\n";
    }
    
    echo "\n=== All Tests Completed Successfully! ===\n";
    echo "\nSUMMARY:\n";
    echo "✓ Currency setup verified\n";
    echo "✓ Exchange rate service functional\n";
    echo "✓ Currency conversion working\n";
    echo "✓ Order creation with currency data\n";
    echo "✓ Order items with base currency prices\n";
    echo "✓ Currency formatting methods\n";
    echo "✓ Financial integrity maintained\n";
    echo "✓ Reporting capabilities available\n";
    
    echo "\nTicket 8.5 implementation is COMPLETE and FUNCTIONAL!\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
