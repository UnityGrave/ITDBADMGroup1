@php
    $stock = $product->inventory->stock ?? 0;
    $rarity = $product->card->rarity ?? null;
    $rarityIcon = match($rarity->name ?? '') {
        'Secret Rare' => '✨',
        'Ultra Rare' => '🌟',
        'Rare' => '★',
        'Uncommon' => '⬡',
        'Common' => '●',
        default => '❓'
    };
    
    // Get price in selected currency using hybrid logic
    $priceObject = $this->getPriceInCurrency();
    $formattedPrice = $priceObject->format();
@endphp

<div class="h-full flex flex-col">
    <!-- Card Image -->
    <div class="aspect-[1/1] bg-brand-gray-100 rounded-t-lg overflow-hidden">
        <a href="{{ route('products.show', ['product' => $product->sku]) }}" class="block w-full h-full">
            @if($product->image_url ?? false)
                <img 
                    src="{{ $product->image_url }}" 
                    alt="{{ $product->card->name }}" 
                    class="w-full h-full object-contain transform group-hover:scale-110 transition-transform duration-300" 
                />
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-4xl text-brand-gray-300">🃏</span>
                </div>
            @endif
        </a>
    </div>

    <!-- Card Details -->
    <div class="flex-1 p-3 flex flex-col">
        <!-- Title and Set -->
        <div class="mb-1.5">
            <a href="{{ route('products.show', ['product' => $product->sku]) }}" class="block">
                <h3 class="font-display font-bold text-pokemon-black text-base leading-tight line-clamp-1 hover:text-pokemon-red transition-colors duration-200">
                    {{ $product->card->name ?? 'Product' }}
                </h3>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-base leading-none">{{ $rarityIcon }}</span>
                    <p class="text-xs text-brand-gray-600 line-clamp-1">
                        {{ $product->card->set->name ?? 'Unknown Set' }}
                    </p>
                </div>
            </a>
        </div>

        <!-- Price, Condition, and Stock -->
        <div class="mt-auto space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-brand-gray-700">
                    {{ $product->condition->value ?? 'Unknown' }} · {{ $rarity->name ?? 'Unknown Rarity' }}
                </span>
            </div>
            
            <div class="flex items-center justify-between gap-2">
                <!-- Multi-currency price display -->
                <div class="flex flex-col">
                    <span class="font-display font-bold text-pokemon-black">
                        {{ $product->getPriceForCurrency(App\Models\Currency::getActiveCurrency())->format() }}
                    </span>
                    @if($currency !== $product->base_currency_code)
                        <span class="text-xs text-brand-gray-500">
                            ({{ $product->base_currency_code }} {{ $product->getFormattedBasePriceAttribute() }})
                        </span>
                    @endif
                </div>
                
                <span class="text-xs px-1.5 py-0.5 rounded-full whitespace-nowrap
                    @if($stock === 0) bg-red-100 text-red-700
                    @elseif($stock < 5) bg-konbini-orange/10 text-konbini-orange
                    @else bg-konbini-green/10 text-konbini-green
                    @endif"
                >
                    @if($stock === 0)
                        Out of Stock
                    @elseif($stock < 5)
                        {{ $stock }} left
                    @else
                        In Stock
                    @endif
                </span>
            </div>

            <!-- Quantity Selector -->
            <div class="flex items-center justify-between gap-2" onclick="event.stopPropagation();">
                <div class="flex items-center bg-brand-gray-50 rounded-lg overflow-hidden">
                    <button 
                        wire:click="updateQuantity({{ max(1, $quantity - 1) }})"
                        type="button"
                        class="p-1.5 hover:bg-brand-gray-200 transition-colors duration-150 text-brand-gray-600"
                        @if($quantity <= 1) disabled @endif
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    
                    <input 
                        type="number" 
                        min="1" 
                        max="{{ $stock }}" 
                        wire:model.blur="quantity"
                        class="w-12 text-center bg-transparent border-0 text-sm font-medium text-brand-gray-900 focus:ring-0"
                        @if($stock === 0) disabled @endif
                    />
                    
                    <button 
                        wire:click="updateQuantity({{ min($stock, $quantity + 1) }})"
                        type="button" 
                        class="p-1.5 hover:bg-brand-gray-200 transition-colors duration-150 text-brand-gray-600"
                        @if($quantity >= $stock) disabled @endif
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Add to Cart Button -->
            <button
                wire:click="addToCart"
                :disabled="{{ $stock === 0 ? 'true' : 'false' }}"
                class="w-full bg-konbini-green text-white py-2 rounded-md text-sm font-medium 
                    hover:bg-green-600 transition-colors duration-200
                    disabled:bg-brand-gray-200 disabled:text-brand-gray-400 disabled:cursor-not-allowed
                    relative z-30"
                onclick="event.stopPropagation();"
            >
                @if($stock === 0)
                    Out of Stock
                @else
                    Add {{ $quantity > 1 ? $quantity . ' ' : '' }}to Cart
                @endif
            </button>
        </div>
    </div>
    

</div> 