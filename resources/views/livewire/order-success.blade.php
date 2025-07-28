{{-- Order Success Page --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Success Header --}}
        <div class="text-center mb-8">
            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
            <p class="text-lg text-gray-600">Thank you for your purchase. Your order has been confirmed.</p>
        </div>

        {{-- Order Summary Card --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-green-900">Order Confirmation</h2>
                        <p class="text-sm text-green-700">Order #{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-green-700">Placed on</p>
                        <p class="text-sm font-medium text-green-900">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Order Items --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Items Ordered</h3>
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h4>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                    <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->formatted_total_price }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Totals --}}
                <div class="border-t border-gray-200 pt-6">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-900">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="text-gray-900">{{ $order->formatted_tax_amount }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-pokemon-red">{{ $order->formatted_total }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 text-right">
                                Currency: {{ $order->currency_code ?? 'USD' }}
                                @if($order->exchange_rate && $order->exchange_rate != 1.0)
                                    (Rate: {{ number_format($order->exchange_rate, 4) }})
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shipping Information --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Shipping Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Shipping Address</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                                            <p>{{ $order->shipping_address_line_1 }}</p>
                @if($order->shipping_address_line_2)
                    <p>{{ $order->shipping_address_line_2 }}</p>
                @endif
                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                <p>{{ $order->shipping_country }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Contact Information</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                                            <p>{{ $order->shipping_email }}</p>
                <p>{{ $order->shipping_phone }}</p>
                        </div>
                    </div>
                </div>

                @if($order->special_instructions)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Special Instructions</h4>
                        <p class="text-sm text-gray-600">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
<div>
                        <p class="text-sm font-medium text-gray-900">Cash on Delivery</p>
                        <p class="text-sm text-gray-600">Pay when your order arrives at your doorstep</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- What's Next Section --}}
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-blue-900 mb-4">What's Next?</h3>
            <div class="space-y-3 text-sm text-blue-800">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p><strong>Order Confirmation:</strong> You'll receive a confirmation email shortly with your order details.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p><strong>Processing:</strong> We'll prepare your order for shipment within 1-2 business days.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 9l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p><strong>Delivery:</strong> Your order will be delivered to your address. Payment is due upon delivery.</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a 
                href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 border border-pokemon-red text-pokemon-red font-medium rounded-lg hover:bg-pokemon-red hover:text-white transition"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Continue Shopping
            </a>
            
            <a 
                href="{{ route('orders.show', $order->id) }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-pokemon-red hover:bg-red-600 text-white font-medium rounded-lg transition"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                View Order Details
            </a>
        </div>

        {{-- Support Information --}}
        <div class="text-center mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Questions about your order? 
                <a href="mailto:support@konibui.com" class="text-pokemon-red hover:text-red-600 font-medium">Contact Support</a>
            </p>
        </div>
    </div>
</div>
