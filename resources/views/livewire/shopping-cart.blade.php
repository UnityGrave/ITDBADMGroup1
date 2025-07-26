{{-- Shopping Cart Component --}}
<div x-data="{ cartOpen: @entangle('isOpen') }" class="relative">
    
    {{-- Cart Trigger Button --}}
    <button 
        @click="cartOpen = true" 
        class="inline-flex items-center bg-pokemon-red hover:bg-red-600 text-white font-medium text-sm px-4 py-2 rounded-lg shadow transition group relative"
    >
        {{-- Trainer's Bag Icon --}}
        <svg class="h-5 w-5 mr-2 transition-transform group-hover:scale-110" viewBox="0 0 24 24" fill="none">
            {{-- Bag Rectangle --}}
            <rect x="4" y="6" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
            {{-- Handle --}}
            <path d="M9 6C9 4.34315 10.3431 3 12 3C13.6569 3 15 4.34315 15 6" stroke="currentColor" stroke-width="1.5"/>
            {{-- Poké Ball --}}
            <circle cx="12" cy="13" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9 13h6" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="12" cy="13" r="1" fill="currentColor"/>
        </svg>
        
        <span>Cart</span>
        
        {{-- Cart Badge --}}
        @if($cartCount > 0)
            <span class="absolute -top-2 -right-2 bg-yellow-400 text-pokemon-black text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-white">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </button>

    {{-- Cart Slide-out Panel --}}
    <div 
        x-show="cartOpen" 
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-xl"
        style="display: none;"
    >
        {{-- Overlay --}}
        <div 
            x-show="cartOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="cartOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
        ></div>

        {{-- Cart Panel Content --}}
        <div class="flex flex-col h-full relative z-50 bg-white">
            {{-- Header --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="h-6 w-6 text-pokemon-red" viewBox="0 0 24 24" fill="none">
                        <rect x="4" y="6" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M9 6C9 4.34315 10.3431 3 12 3C13.6569 3 15 4.34315 15 6" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="12" cy="13" r="3" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M9 13h6" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="12" cy="13" r="1" fill="currentColor"/>
                    </svg>
                    <h2 class="text-lg font-bold text-gray-900">Trainer's Bag</h2>
                </div>
                
                <button 
                    @click="cartOpen = false"
                    class="p-2 hover:bg-gray-100 rounded-full transition"
                >
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4">
                @if($cartCount > 0)
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                {{-- Product Image Placeholder --}}
                                <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item['product']['card']['name'] ?? 'Product Name' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        ${{ number_format($item['product']['price'] ?? 0, 2) }}
                                    </p>
                                    
                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center space-x-2 mt-2">
                                        <button 
                                        wire:click="updateQuantity({{ $item['product_id'] }}, {{ ($item['quantity'] ?? 1) - 1 }})"
                                        class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        
                                        <span class="w-8 text-center text-sm font-medium">{{ $item['quantity'] ?? 1 }}</span>

                                        <button 
                                            wire:click="updateQuantity({{ $item['product_id'] }}, {{ ($item['quantity'] ?? 1) + 1 }})"
                                            class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Remove Button --}}
                                <button 
                                    wire:click="removeItem({{ $item['product_id'] }})"
                                    class="p-1 hover:bg-red-100 rounded-full text-red-500 hover:text-red-700 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Clear Cart Button --}}
                    <div class="mt-6">
                        <button 
                            wire:click="clearCart"
                            wire:confirm="Are you sure you want to clear your cart?"
                            class="w-full text-sm text-red-600 hover:text-red-800 font-medium py-2 border border-red-200 hover:border-red-300 rounded-lg transition"
                        >
                            Clear Cart
                        </button>
                    </div>
                @else
                    {{-- Empty Cart State --}}
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">Your bag is empty</h3>
                        <p class="mt-2 text-sm text-gray-500">Start collecting cards to fill your trainer's bag!</p>
                        <button 
                            @click="cartOpen = false"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-pokemon-red text-white text-sm font-medium rounded-lg hover:bg-red-600 transition"
                        >
                            Continue Shopping
                        </button>
                    </div>
                @endif
            </div>

            {{-- Footer with Total and Checkout --}}
            @if($cartCount > 0)
                <div class="border-t border-gray-200 p-4 space-y-4">
                    {{-- Subtotal --}}
                    <div class="flex justify-between items-center">
                        <span class="text-base font-medium text-gray-900">Subtotal</span>
                        <span class="text-lg font-bold text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                    </div>

                    {{-- Checkout Button --}}
                    <button 
                        wire:click="checkout"
                        class="w-full bg-pokemon-red hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg shadow transition transform hover:scale-105 active:scale-95"
                    >
                        Proceed to Checkout
                    </button>

                    {{-- Continue Shopping Link --}}
                    <button 
                        @click="cartOpen = false"
                        class="w-full text-sm text-gray-600 hover:text-gray-800 font-medium py-2 transition"
                    >
                        Continue Shopping
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Toast Notification Script --}}
<script>
    window.showToast = function(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Hide and remove toast
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    };
</script>
