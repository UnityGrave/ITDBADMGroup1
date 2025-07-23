<x-app-layout>
    <form method="GET" action="{{ route('products.index') }}" class="max-w-7xl mx-auto py-10 px-4 flex gap-8">
        <aside class="w-64 shrink-0 hidden md:block">
            <div class="space-y-6">
                <div>
                    <h3 class="font-semibold mb-2">Category</h3>
                    @foreach ($categories as $category)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                @if (in_array($category->id, request()->input('categories', []))) checked @endif>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Set</h3>
                    @foreach ($sets as $set)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="sets[]" value="{{ $set->id }}"
                                @if (in_array($set->id, request()->input('sets', []))) checked @endif>
                            <span>{{ $set->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Rarity</h3>
                    <select name="rarity" class="w-full border rounded">
                        <option value="">Any</option>
                        @foreach ($rarities as $rarity)
                            <option value="{{ $rarity->id }}" @if (request('rarity') == $rarity->id) selected @endif>
                                {{ $rarity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                        Apply Filters
                    </button>
                </div>
            </div>
        </aside>

        <div class="flex-1">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 drop-shadow">Shop All Products</h1>
                <div>
                    <select name="sort" class="border rounded px-2 py-1" onchange="this.form.submit()">
                        <option value="created_at_desc" @if ($sort === 'created_at_desc') selected @endif>Newest</option>
                        <option value="price_asc" @if ($sort === 'price_asc') selected @endif>Price: Low to High</option>
                        <option value="price_desc" @if ($sort === 'price_desc') selected @endif>Price: High to Low</option>
                        <option value="name_asc" @if ($sort === 'name_asc') selected @endif>Name: A-Z</option>
                        <option value="set_asc" @if ($sort === 'set_asc') selected @endif>Set</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 mb-8">
                @forelse ($products as $product)
                    <a href="{{ route('products.show', ['product' => $product->sku]) }}" class="block hover:scale-105 transition">
                        <x-product-card :product="$product" />
                    </a>
                @empty
                    <p class="col-span-full text-center text-gray-600">No products match your filters.</p>
                @endforelse
            </div>

            <div class="flex justify-center mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </form>
</x-app-layout>
