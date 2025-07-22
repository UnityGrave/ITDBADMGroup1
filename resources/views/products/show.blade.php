<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
    <a href="{{ route('products.index') }}"
           class="inline-flex items-center text-sm text-gray-700 hover:text-primary transition mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Products
        </a>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Left Side: Image -->
            <div class="md:w-1/2 bg-gray-100 p-4 rounded">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->card->name }}" class="w-full h-auto object-contain" />
                @else
                    <div class="w-full h-80 flex items-center justify-center bg-gray-200 rounded">
                        <span class="text-7xl text-gray-400">🃏</span>
                    </div>
                @endif
            </div>

            <!-- Right Side: Product Details -->
            <div class="md:w-1/2 flex flex-col space-y-4">
                <h1 class="text-3xl font-extrabold text-gray-900">{{ $product->card->name }}</h1>

                <p class="text-sm text-gray-500">
                    Set: <span class="font-medium text-gray-700">{{ $product->card->set->name ?? 'Unknown Set' }}</span> <br>
                    Rarity: <span class="font-medium text-gray-700">{{ $product->card->rarity->name ?? 'Unknown' }}</span> <br>
                    Category: <span class="font-medium text-gray-700">{{ $product->card->category->name ?? 'Unknown' }}</span>
                </p>

                <p class="text-2xl font-bold text-primary">
                    ${{ number_format($product->price, 2) }}
                </p>

                <p class="text-sm">
                    @if($product->inventory->stock === 0)
                        <span class="text-red-600 font-semibold">Out of Stock</span>
                    @elseif($product->inventory->stock < 5)
                        <span class="text-yellow-600 font-semibold">Low Stock ({{ $product->inventory->stock }})</span>
                    @else
                        <span class="text-green-600 font-semibold">In Stock ({{ $product->inventory->stock }})</span>
                    @endif
                </p>

                <button class="bg-green-600 text-white px-4 py-2 rounded font-semibold hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
