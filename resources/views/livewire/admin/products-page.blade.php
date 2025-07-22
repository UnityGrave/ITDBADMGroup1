<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <x-product-management-header />
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Product Variants & Inventory</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage product variants with different conditions and inventory levels</p>
                </div>
                <div class="text-sm bg-orange-100 text-orange-800 px-3 py-1 rounded-full">
                    {{ $products->count() }} Products
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Product Variant' : 'Add New Product Variant' }}
                </h3>

                <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="card_id" class="block text-sm font-medium text-gray-700">Card</label>
                            <select wire:model.defer="card_id"
                                    id="card_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select a card</option>
                                @foreach($cards as $card)
                                    <option value="{{ $card->id }}">{{ $card->name }} ({{ $card->collector_number }})</option>
                                @endforeach
                            </select>
                            @error('card_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
                            <select wire:model.defer="condition"
                                    id="condition"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Select condition</option>
                                @foreach($conditions as $cond)
                                    <option value="{{ $cond->value }}">{{ $cond->value }} - {{ $cond->name ?? $cond->value }}</option>
                                @endforeach
                            </select>
                            @error('condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                            <input type="number"
                                   wire:model.defer="price"
                                   id="price"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" />
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                            <input type="text"
                                   wire:model.defer="sku"
                                   id="sku"
                                   placeholder="e.g., BASE-CHAR-NM-001"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" />
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                            <input type="number"
                                   wire:model.defer="stock"
                                   id="stock"
                                   min="0"
                                   placeholder="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" />
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        @if($editingId)
                            <button type="button"
                                    wire:click="$set('editingId', null)"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Cancel
                            </button>
                        @endif
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            {{ $editingId ? 'Update Product' : 'Add Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Product Variants</h3>

                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->card->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->card->collector_number ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if(isset($product->condition))
                                                    @switch($product->condition->value)
                                                        @case('NM') bg-green-100 text-green-800 @break
                                                        @case('LP') bg-blue-100 text-blue-800 @break
                                                        @case('MP') bg-yellow-100 text-yellow-800 @break
                                                        @case('HP') bg-orange-100 text-orange-800 @break
                                                        @case('DMG') bg-red-100 text-red-800 @break
                                                        @default bg-gray-100 text-gray-800
                                                    @endswitch
                                                @else
                                                    bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $product->condition->value ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->sku }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if(($product->inventory->stock ?? 0) > 10) bg-green-100 text-green-800
                                                @elseif(($product->inventory->stock ?? 0) > 0) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $product->inventory->stock ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $product->id }})"
                                                    class="text-orange-600 hover:text-orange-900 mr-3">
                                                Edit
                                            </button>
                                            <button wire:click="delete({{ $product->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this product?')"
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No product variants available</h3>
                        <p class="mt-1 text-sm text-gray-500">Create your first product variant to start managing inventory.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
