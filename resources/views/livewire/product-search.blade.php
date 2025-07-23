<div 
    x-data="{ 
        open: @entangle('showResults'),
        query: @entangle('query'),
        selected: -1,
        items: [],
        init() {
            this.$watch('items', value => {
                if (value.length > 0 && this.selected >= value.length) {
                    this.selected = value.length - 1;
                }
            });
        },
        onKeyDown(e) {
            if (e.key === 'ArrowDown') {
                this.selected = (this.selected + 1) % this.items.length;
            } else if (e.key === 'ArrowUp') {
                this.selected = this.selected > 0 ? this.selected - 1 : this.items.length - 1;
            } else if (e.key === 'Enter' && this.selected >= 0) {
                window.location.href = this.items[this.selected].href;
            } else if (e.key === 'Escape') {
                this.open = false;
                this.selected = -1;
            }
        }
    }" 
    class="relative"
    @click.away="open = false"
>
    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.500ms="query"
            placeholder="Search products..."
            @focus="open = true"
            @keydown="onKeyDown($event)"
            class="w-full pl-10 pr-4 py-2 text-sm text-brand-gray-900 bg-white border border-brand-gray-200 rounded-lg focus:outline-none focus:border-pokemon-red focus:ring-1 focus:ring-pokemon-red"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-brand-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
        @if($query)
            <button 
                wire:click="clearSearch"
                type="button" 
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <svg class="h-5 w-5 text-brand-gray-400 hover:text-brand-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg overflow-hidden"
        style="display: none;"
    >
        <div wire:loading class="p-4 text-sm text-brand-gray-500 text-center">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-pokemon-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Searching...
            </div>
        </div>

        <div wire:loading.remove>
            @if($this->searchResults->isEmpty())
                @if(strlen($query) >= 2)
                    <div class="p-4 text-sm text-brand-gray-500 text-center">
                        No products found for "{{ $query }}"
                    </div>
                @endif
            @else
                <ul 
                    class="max-h-96 overflow-y-auto"
                    x-ref="results"
                    x-init="items = $refs.results.querySelectorAll('a')"
                >
                    @foreach($this->searchResults as $product)
                        <li>
                            <a 
                                href="{{ route('products.show', $product->sku) }}"
                                class="block px-4 py-3 hover:bg-brand-gray-50 transition-colors duration-150 {{ $loop->first ? 'rounded-t-lg' : '' }} {{ $loop->last ? 'rounded-b-lg' : '' }}"
                                :class="{ 'bg-brand-gray-50': selected === {{ $loop->index }} }"
                            >
                                <div class="flex items-center">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-pokemon-black truncate">
                                            {{ $product->card->name }}
                                        </p>
                                        <p class="text-xs text-brand-gray-500">
                                            {{ $product->card->set->name }} · {{ $product->card->rarity->name }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <p class="text-sm font-medium text-pokemon-red">${{ number_format($product->price, 2) }}</p>
                                        <p class="text-xs text-brand-gray-500 text-right">
                                            {{ $product->condition->value }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if($this->searchResults->count() >= 5)
                    <div class="p-3 bg-brand-gray-50 border-t border-brand-gray-100">
                        <a 
                            href="{{ route('products.index', ['search' => $query]) }}" 
                            class="block text-center text-sm text-pokemon-red hover:text-red-600 font-medium"
                        >
                            View all results
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
