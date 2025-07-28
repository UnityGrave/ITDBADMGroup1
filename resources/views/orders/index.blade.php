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
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($order->status === 'refunded') bg-gray-100 text-gray-800
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
                                    <button onclick="openEditModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->payment_status }}')" 
                                            class="text-gray-600 hover:text-gray-500">Edit Status</button>
                                @endif
                                @if(auth()->user()->hasRole('Admin'))
                                    <button onclick="confirmDelete({{ $order->id }}, '{{ $order->order_number }}')" 
                                            class="text-red-600 hover:text-red-500">Delete</button>
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
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        {{ $orders->count() }}
                    @else
                        {{ $orders->where('user_id', auth()->id())->count() }}
                    @endif
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
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        ${{ number_format($orders->sum('total_amount'), 2) }}
                    @else
                        ${{ number_format($orders->where('user_id', auth()->id())->sum('total_amount'), 2) }}
                    @endif
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
                <p class="text-2xl font-bold text-gray-900">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        {{ $orders->where('status', 'pending')->count() }}
                    @else
                        {{ $orders->where('user_id', auth()->id())->where('status', 'pending')->count() }}
                    @endif
                </p>
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
                <p class="text-2xl font-bold text-gray-900">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        {{ $orders->where('status', 'delivered')->count() }}
                    @else
                        {{ $orders->where('user_id', auth()->id())->where('status', 'delivered')->count() }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<x-modal name="editStatusModal" :show="false">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Order Status</h2>
        
        <form id="editStatusForm" onsubmit="updateOrderStatus(event)">
            @csrf
            <input type="hidden" id="editOrderId" name="order_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="editOrderStatus" class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select id="editOrderStatus" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                
                <div>
                    <label for="editPaymentStatus" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select id="editPaymentStatus" name="payment_status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="editNotes" name="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes about this status change..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</x-modal>

<!-- Delete Confirmation Modal -->
<x-modal name="deleteOrderModal" :show="false">
    <div class="p-6">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Delete Order</h3>
            </div>
        </div>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Are you sure you want to delete order <span id="deleteOrderNumber" class="font-medium"></span>? This action cannot be undone.
            </p>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" onclick="deleteOrder()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                Delete Order
            </button>
        </div>
    </div>
</x-modal>

<script>
let currentOrderId = null;

function openEditModal(orderId, currentStatus, currentPaymentStatus) {
    currentOrderId = orderId;
    document.getElementById('editOrderId').value = orderId;
    document.getElementById('editOrderStatus').value = currentStatus;
    document.getElementById('editPaymentStatus').value = currentPaymentStatus;
    
    // Trigger the modal to open
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'editStatusModal'
    }));
}

function closeEditModal() {
    window.dispatchEvent(new CustomEvent('close-modal', {
        detail: 'editStatusModal'
    }));
    currentOrderId = null;
}

function confirmDelete(orderId, orderNumber) {
    currentOrderId = orderId;
    document.getElementById('deleteOrderNumber').textContent = '#' + orderNumber;
    
    // Trigger the modal to open
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'deleteOrderModal'
    }));
}

function closeDeleteModal() {
    window.dispatchEvent(new CustomEvent('close-modal', {
        detail: 'deleteOrderModal'
    }));
    currentOrderId = null;
}

async function updateOrderStatus(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('{{ route("orders.update-status") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            alert('Order status updated successfully!');
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while updating the order status.');
        console.error('Error:', error);
    }
    
    closeEditModal();
}

async function deleteOrder() {
    if (!currentOrderId) return;
    
    try {
        const response = await fetch(`/orders/${currentOrderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            alert('Order deleted successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while deleting the order.');
        console.error('Error:', error);
    }
    
    closeDeleteModal();
}
</script>
</div>
</div>
</x-app-layout>