@props(['product'])

@php
    use App\Models\Currency;

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
    
    // Get current active currency
    $currentCurrency = Currency::getActiveCurrency();
    
    // Get price in current currency using the same logic as Livewire components
    $priceObject = $product->getPriceForCurrency($currentCurrency);
    $formattedPrice = $priceObject->format();
@endphp

<div class="h-full flex flex-col">
    <!-- Card Image -->
    <div class="aspect-[1/1] bg-brand-gray-100 rounded-t-lg overflow-hidden">
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
    </div>

    <!-- Card Details -->
    <div class="flex-1 p-3 flex flex-col">
        <!-- Title and Set -->
        <div class="mb-1.5">
            <h3 class="font-display font-bold text-pokemon-black text-base leading-tight line-clamp-1">
                {{ $product->card->name ?? 'Product' }}
            </h3>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span class="text-base leading-none">{{ $rarityIcon }}</span>
                <p class="text-xs text-brand-gray-600 line-clamp-1">
                    {{ $product->card->set->name ?? 'Unknown Set' }}
                </p>
            </div>
        </div>

        <!-- Price, Condition, and Stock -->
        <div class="mt-auto space-y-2" x-data="{ quantity: 1, caught: false }">
            <div class="flex items-center justify-between text-sm">
                <span class="text-brand-gray-700">
                    {{ $product->condition->value ?? 'Unknown' }} · {{ $rarity->name ?? 'Unknown Rarity' }}
                </span>
            </div>
            
            <div class="flex items-center justify-between gap-2">
                <!-- Multi-currency price display -->
                <div class="flex flex-col">
                    <span class="font-display font-bold text-pokemon-black">
                        {{ $formattedPrice }}
                    </span>
                    @if($currentCurrency !== $product->base_currency_code)
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
            <!-- Add to Cart Button -->
            <button
                @click.stop="
                    $wire.dispatch('add-to-cart', { productId: {{ $product->id }}, quantity: quantity });
                    caught = true;
                    setTimeout(() => caught = false, 1200);
                "
                :disabled="{{ $stock === 0 ? 'true' : 'false' }}"
                class="pointer-events-auto w-full bg-konbini-green text-white py-2 rounded-md text-sm font-medium 
                    hover:bg-green-600 transition-colors duration-200
                    disabled:bg-brand-gray-200 disabled:text-brand-gray-400 disabled:cursor-not-allowed
                    relative z-20"
            >
                @if($stock === 0)
                    Out of Stock
                @else
                    <span x-show="!caught">Add <span x-text="quantity > 1 ? quantity + ' ' : ''"></span>to Cart</span>
                    <span x-show="caught" class="flex items-center justify-center gap-1">
                        <span>Caught!</span>
                        <span class="animate-spin"><x-loading-spinner /></span>
                    </span>
                @endif
            </button>
        </div>
    </div>
</div>
