<x-app-layout>
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> 
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Order History</h2>
            <p class="text-gray-600 text-sm mt-1">View and manage your order history</p>
        </div>
        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
            <div class="text-sm text-blue-600">
                Viewing: All Orders (Staff View)
            </div>
        @endif
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Items
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M j, Y') }}
                            </td>
                            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->user->name }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->orderItems->pluck('product_name')->take(2)->join(', ') }}
                                @if($order->orderItems->count() > 2)
                                    <span class="text-gray-400">+{{ $order->orderItems->count() - 2 }} more</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium 
                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif
                                    rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-500">View</a>
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                                        <button class="text-gray-600 hover:text-gray-500">Edit Status</button>
                                    @endif
                                    @if(auth()->user()->hasRole('Admin'))
                                        <button class="text-red-600 hover:text-red-500">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee') ? '7' : '6' }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                                    <p class="text-gray-500 mb-4">You haven't placed any orders yet. Start shopping to see your order history here.</p>
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-konbini-green text-white text-sm font-medium rounded-md hover:bg-green-600 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Start Shopping
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Order Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="bg-blue-100 p-2 rounded-lg">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $orders->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="bg-green-100 p-2 rounded-lg">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                <p class="text-2xl font-bold text-gray-900">
                    ${{ number_format($orders->sum('total_amount'), 2) }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="bg-yellow-100 p-2 rounded-lg">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
                <p class="text-2xl font-bold text-gray-900">2</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="bg-purple-100 p-2 rounded-lg">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                <p class="text-2xl font-bold text-gray-900">1</p>
            </div>
        </div>
    </div>
</div>

<!-- RBAC Testing Information -->
<div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Role-Based Access Control Test</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
            <span class="font-medium text-gray-700">Current User:</span>
            <p class="text-gray-600">
                {{ auth()->user()->name }}
                @if(auth()->user()->roles->count() > 0)
                    ({{ auth()->user()->roles->pluck('name')->join(', ') }})
                @endif
            </p>
        </div>
        <div>
            <span class="font-medium text-gray-700">Order Visibility:</span>
            <ul class="text-gray-600 mt-1">
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                    <li>✅ Can view all orders</li>
                    <li>✅ Can see customer details</li>
                    <li>✅ Can edit order status</li>
                @else
                    <li>✅ Can view own orders only</li>
                    <li>❌ Cannot see other customer orders</li>
                    <li>❌ Cannot edit order status</li>
                @endif
            </ul>
        </div>
        <div>
            <span class="font-medium text-gray-700">Order Actions:</span>
            <ul class="text-gray-600 mt-1">
                <li>✅ Can view order details</li>
                <li>✅ Can cancel own pending orders</li>
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                    <li>✅ Can process orders</li>
                    <li>✅ Can update order status</li>
                @else
                    <li>❌ Cannot process orders</li>
                    <li>❌ Cannot update order status</li>
                @endif
                @if(auth()->user()->hasRole('Admin'))
                    <li>✅ Can delete orders</li>
                @else
                    <li>❌ Cannot delete orders</li>
                @endif
            </ul>
        </div>
    </div>
</div>
</div>
</div>
</x-app-layout>