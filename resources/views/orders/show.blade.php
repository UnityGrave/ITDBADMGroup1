<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Order Details</h2>
                        <p class="text-gray-600 text-sm mt-1">Order #{{ $order->order_number }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 text-sm font-medium 
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif
                            rounded-full">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Back button -->
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Orders
                </a>
            </div>

            <!-- Order Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Order Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Items Ordered</h3>
                        <div class="space-y-4">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center space-x-4 py-4 border-b border-gray-200 last:border-b-0">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white text-xl font-bold">🃏</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h4>
                                        <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">${{ number_format($item->unit_price, 2) }} each</p>
                                        <p class="text-sm font-medium text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary & Details -->
                <div class="space-y-6">
                    
                    <!-- Order Summary -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="text-gray-900">${{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax</span>
                                <span class="text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between text-base font-medium">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-pokemon-red">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Order Date:</span>
                                <span class="text-gray-900 ml-2">{{ $order->created_at->format('M j, Y \a\t g:i A') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="text-gray-900 ml-2">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="text-gray-900 ml-2">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                            <p>{{ $order->shipping_address_line_1 }}</p>
                            @if($order->shipping_address_line_2)
                                <p>{{ $order->shipping_address_line_2 }}</p>
                            @endif
                            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                            <p>{{ $order->shipping_country }}</p>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p>{{ $order->shipping_email }}</p>
                                <p>{{ $order->shipping_phone }}</p>
                            </div>
                        </div>
                    </div>

                    @if($order->special_instructions)
                        <!-- Special Instructions -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Special Instructions</h3>
                            <p class="text-sm text-gray-600">{{ $order->special_instructions }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 