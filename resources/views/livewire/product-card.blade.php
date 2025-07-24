{{-- Product Card Component --}}
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
    {{-- Product Image --}}
    <div class="aspect-w-1 aspect-h-1 bg-gradient-to-br from-blue-400 to-purple-500 relative overflow-hidden">
        <div class="flex items-center justify-center h-48">
            {{-- Placeholder for product image --}}
            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        
        {{-- Quick Add Button (appears on hover) --}}
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
            <button 
                wire:click="quickAddToCart"
                class="bg-pokemon-red hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 shadow-lg"
            >
                Quick Add
            </button>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="p-4">
        {{-- Product Name --}}
        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ $product->name ?? 'Product Name' }}
        </h3>

        {{-- Product Details --}}
        <div class="space-y-1 mb-3">
            @if($product->category ?? false)
                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                    {{ $product->category->name }}
                </span>
            @endif
            
            @if($product->condition ?? false)
                <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded ml-1">
                    {{ $product->condition->value ?? 'New' }}
                </span>
            @endif
        </div>

        {{-- Price --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl font-bold text-pokemon-red">
                ${{ number_format($product->price ?? 0, 2) }}
            </span>
            
            @if($product->inventory && $product->inventory->stock_quantity <= 5)
                <span class="text-xs text-orange-600 font-medium">
                    Only {{ $product->inventory->stock_quantity ?? 0 }} left!
                </span>
            @endif
        </div>

        {{-- Add to Cart Section --}}
        <div class="flex items-center space-x-2">
            {{-- Quantity Selector --}}
            <div class="flex items-center border border-gray-300 rounded-lg">
                <button 
                    wire:click="updateQuantity({{ $quantity - 1 }})"
                    class="p-2 hover:bg-gray-100 transition disabled:opacity-50"
                    {{ $quantity <= 1 ? 'disabled' : '' }}
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                
                <input 
                    type="number" 
                    wire:model="quantity"
                    wire:change="updateQuantity($event.target.value)"
                    min="1"
                    max="{{ $product->inventory->stock_quantity ?? 999 }}"
                    class="w-16 text-center border-0 focus:ring-0 py-2"
                />
                
                <button 
                    wire:click="updateQuantity({{ $quantity + 1 }})"
                    class="p-2 hover:bg-gray-100 transition disabled:opacity-50"
                    {{ $quantity >= ($product->inventory->stock_quantity ?? 999) ? 'disabled' : '' }}
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>

            {{-- Add to Cart Button --}}
            <button 
                wire:click="addToCart"
                class="flex-1 bg-pokemon-red hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:transform-none"
                {{ ($product->inventory->stock_quantity ?? 0) <= 0 ? 'disabled' : '' }}
            >
                @if(($product->inventory->stock_quantity ?? 0) <= 0)
                    Out of Stock
                @else
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Add to Bag
                @endif
            </button>
        </div>

        {{-- Product Description (if available) --}}
        @if($product->description ?? false)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-sm text-gray-600 line-clamp-2">
                    {{ $product->description }}
                </p>
            </div>
        @endif
    </div>
</div>
