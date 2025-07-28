<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <a href="{{ route('products.index') }}"
        class="inline-flex items-center text-brand-gray-700 hover:text-pokemon-red transition mb-8 group"
    >
        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span class="font-medium">Back to Products</span>
    </a>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Image -->
            <div class="lg:w-1/2 bg-gradient-to-br from-pokemon-red/5 to-konbini-green/5 p-8 flex items-center justify-center">
                <div class="relative w-full max-w-md">
                    <div class="absolute inset-0 bg-gradient-to-br from-pokemon-red/20 to-konbini-green/20 rounded-lg transform rotate-3"></div>
                    @if ($product->image_url)
                        <img 
                            src="{{ $product->image_url }}" 
                            alt="{{ $product->card->name }}" 
                            class="relative rounded-lg shadow-xl transform -rotate-3 transition-transform duration-300 hover:rotate-0 w-full h-auto object-contain"
                        />
                    @else
                        <div class="relative w-full aspect-[3/4] bg-brand-gray-100 rounded-lg shadow-xl transform -rotate-3 transition-transform duration-300 hover:rotate-0 flex items-center justify-center">
                            <span class="text-7xl text-brand-gray-300">🃏</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="lg:w-1/2 p-8 flex flex-col">
                <div class="flex-1">
                    <!-- Title and Category -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-display font-bold text-pokemon-black mb-2">{{ $product->card->name }}</h1>
                        <div class="flex items-center text-brand-gray-600">
                            <span class="px-2 py-1 bg-brand-gray-100 rounded-full text-sm">
                                {{ $product->card->category->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Set and Rarity -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center justify-between py-3 border-b border-brand-gray-100">
                            <span class="text-brand-gray-600">Set</span>
                            <span class="font-medium text-pokemon-black">{{ $product->card->set->name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-brand-gray-100">
                            <span class="text-brand-gray-600">Rarity</span>
                            <span class="font-medium text-pokemon-black">{{ $product->card->rarity->name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-brand-gray-100">
                            <span class="text-brand-gray-600">Condition</span>
                            <span class="font-medium text-pokemon-black">{{ $product->condition->value }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-brand-gray-100">
                            <span class="text-brand-gray-600">SKU</span>
                            <span class="font-medium text-pokemon-black">{{ $product->sku }}</span>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-8">
                        @if($product->inventory->stock === 0)
                            <div class="flex items-center gap-2 text-red-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Out of Stock</span>
                            </div>
                        @elseif($product->inventory->stock < 5)
                            <div class="flex items-center gap-2 text-konbini-orange">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Only {{ $product->inventory->stock }} left in stock</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-konbini-green">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">In Stock ({{ $product->inventory->stock }} available)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price and Add to Cart -->
                <div class="border-t border-brand-gray-100 pt-6">
                    @php
                        // Get price in selected currency using hybrid logic
                        $priceObject = $this->getPriceInCurrency();
                        $formattedPrice = $priceObject->format();
                        $totalPrice = $priceObject->multipliedBy($quantity);
                    @endphp
                    
                    <div class="mb-4">
                        <!-- Current currency price -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-brand-gray-600">Price</span>
                            <span class="text-2xl font-display font-bold text-pokemon-black">{{ $formattedPrice }}</span>
                        </div>
                        
                        <!-- Base currency price (if different) -->
                        @if($currency !== $product->base_currency_code)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-brand-gray-500">Base Price</span>
                                <span class="text-brand-gray-500">{{ $product->base_currency_code }} {{ $product->getFormattedBasePriceAttribute() }}</span>
                            </div>
                        @endif
                        
                        <!-- Currency Selector -->
                        @if(config('app.debug'))
                            <div class="mt-2">
                                <label class="text-sm text-brand-gray-600">Currency:</label>
                                <select wire:model.live="currency" class="ml-2 text-sm border-brand-gray-300 rounded">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="JPY">JPY (¥)</option>
                                    <option value="CAD">CAD (C$)</option>
                                    <option value="AUD">AUD (A$)</option>
                                    <option value="CHF">CHF</option>
                                    <option value="SEK">SEK (kr)</option>
                                    <option value="PHP">PHP (₱)</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    
                    @if($product->inventory->stock > 0)
                        <!-- Quantity Selector -->
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-brand-gray-600">Quantity:</span>
                            <div class="flex items-center border border-brand-gray-300 rounded-lg">
                                <button 
                                    wire:click="decrementQuantity"
                                    class="px-3 py-2 text-brand-gray-600 hover:text-pokemon-red transition-colors"
                                    @if($quantity <= 1) disabled @endif
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input 
                                    type="number" 
                                    wire:model.live="quantity"
                                    min="1" 
                                    max="{{ $product->inventory->stock }}"
                                    class="w-16 px-2 py-2 text-center border-0 focus:ring-0 focus:outline-none"
                                >
                                <button 
                                    wire:click="incrementQuantity"
                                    class="px-3 py-2 text-brand-gray-600 hover:text-pokemon-red transition-colors"
                                    @if($quantity >= $product->inventory->stock) disabled @endif
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                            <span class="text-sm text-brand-gray-500">
                                Total: {{ $totalPrice->format() }}
                            </span>
                        </div>
                    @endif

                    <!-- Add to Cart Button -->
                    <button 
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        @if($product->inventory->stock === 0) disabled @endif
                        class="w-full bg-konbini-green text-white py-3 rounded-lg font-medium hover:bg-green-600 transition-colors duration-200 disabled:bg-brand-gray-200 disabled:text-brand-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="addToCart">
                            @if($product->inventory->stock === 0)
                                Out of Stock
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Add {{ $quantity > 1 ? $quantity . ' items' : '1 item' }} to Trainer's Bag
                            @endif
                        </span>
                        <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Adding to cart...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 