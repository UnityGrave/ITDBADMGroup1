    <div x-data="{ mobileFiltersOpen: false }" class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <!-- Mobile filter dialog -->
        <div 
            x-show="mobileFiltersOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="relative z-40 lg:hidden"
            role="dialog" 
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-black bg-opacity-25"></div>
            <div class="fixed inset-0 z-40 flex">
                <div 
                    x-show="mobileFiltersOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-6 shadow-xl"
                >
                    <div class="flex items-center justify-between px-4">
                        <h2 class="text-lg font-display font-medium text-brand-gray-900">Filters</h2>
                        <button
                            type="button"
                            class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md focus:outline-none focus:ring-2 focus:ring-pokemon-red"
                            @click="mobileFiltersOpen = false"
                        >
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6 text-brand-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Filters -->
                    <div class="mt-4 border-t border-brand-gray-200">
                        <div class="px-4 space-y-6 divide-y divide-brand-gray-200">
                            <div class="pt-6">
                                <h3 class="font-display font-semibold mb-2">Category</h3>
                                @foreach ($this->categories as $category)
                                    <label class="flex items-center space-x-2 py-1">
                                        <input type="checkbox" value="{{ $category->id }}" wire:model.live="filters.categories"
                                            class="rounded border-brand-gray-300 text-pokemon-red focus:ring-pokemon-red">
                                        <span class="text-brand-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="pt-6">
                                <h3 class="font-display font-semibold mb-2">Set</h3>
                                @foreach ($this->sets as $set)
                                    <label class="flex items-center space-x-2 py-1">
                                        <input type="checkbox" value="{{ $set->id }}" wire:model.live="filters.sets"
                                            class="rounded border-brand-gray-300 text-pokemon-red focus:ring-pokemon-red">
                                        <span class="text-brand-gray-700">{{ $set->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="pt-6">
                                <h3 class="font-display font-semibold mb-2">Rarity</h3>
                                <select wire:model.live="filters.rarityId" 
                                    class="w-full rounded-md border-brand-gray-300 focus:border-pokemon-red focus:ring-pokemon-red">
                                    <option value="">Any</option>
                                    @foreach ($this->rarities as $rarity)
                                        <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-8">
            <!-- Desktop Filters -->
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="space-y-6 sticky top-4">
                    <div>
                        <h3 class="font-display font-semibold mb-2">Category</h3>
                        @foreach ($this->categories as $category)
                            <label class="flex items-center space-x-2 py-1">
                                <input type="checkbox" value="{{ $category->id }}" wire:model.live="filters.categories"
                                    class="rounded border-brand-gray-300 text-pokemon-red focus:ring-pokemon-red">
                                <span class="text-brand-gray-700">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <h3 class="font-display font-semibold mb-2">Set</h3>
                        @foreach ($this->sets as $set)
                            <label class="flex items-center space-x-2 py-1">
                                <input type="checkbox" value="{{ $set->id }}" wire:model.live="filters.sets"
                                    class="rounded border-brand-gray-300 text-pokemon-red focus:ring-pokemon-red">
                                <span class="text-brand-gray-700">{{ $set->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <h3 class="font-display font-semibold mb-2">Rarity</h3>
                        <select wire:model.live="filters.rarityId" 
                            class="w-full rounded-md border-brand-gray-300 focus:border-pokemon-red focus:ring-pokemon-red">
                            <option value="">Any</option>
                            @foreach ($this->rarities as $rarity)
                                <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4">
                        <button wire:click="applyFilters"
                            class="w-full bg-konbini-orange text-white py-2 rounded-md hover:bg-orange-600 transition font-medium">
                            Apply Filters
                        </button>
                        <div wire:loading class="flex justify-center items-center gap-2 mt-2">
                            <x-loading-spinner size="sm" class="text-pokemon-red" />
                            <span class="text-sm text-brand-gray-600">Updating results...</span>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-display font-extrabold text-pokemon-black">Shop All Products</h1>
                        <p class="mt-1 text-brand-gray-600">Browse our collection of Pokémon cards</p>
                    </div>
                    
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <!-- Mobile filter button -->
                        <button
                            type="button"
                            class="lg:hidden bg-white px-4 py-2 rounded-md border border-brand-gray-300 text-brand-gray-700 hover:bg-brand-gray-50 flex items-center gap-2"
                            @click="mobileFiltersOpen = true"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.59L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                            Filters
                        </button>

                        <select wire:model.live="filters.sort" 
                            class="rounded-md border-brand-gray-300 focus:border-pokemon-red focus:ring-pokemon-red text-brand-gray-700">
                            <option value="created_at_desc">Newest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name_asc">Name: A-Z</option>
                            <option value="set_asc">Set</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @forelse ($this->products as $product)
                        <a href="{{ route('products.show', ['product' => $product->sku]) }}" 
                            class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                            <x-product-card :product="$product" />
                        </a>
                    @empty
                        <p class="col-span-full text-center py-12 text-brand-gray-500 bg-brand-gray-50 rounded-lg">
                            No products match your filters.
                        </p>
                    @endforelse
                </div>

                <div class="flex justify-center mt-8">
                    <div wire:loading class="flex items-center gap-2">
                        <x-loading-spinner size="md" class="text-pokemon-red" />
                        <span class="text-brand-gray-600">Loading more products...</span>
                    </div>
                    <div wire:loading.remove>
                        {{ $this->products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>