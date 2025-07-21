<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Products</h1>
    <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="mb-4 grid grid-cols-6 gap-2">
        <select wire:model.defer="card_id" class="border rounded px-2 py-1">
            <option value="">Card</option>
            @foreach($cards as $card)
                <option value="{{ $card->id }}">{{ $card->name }}</option>
            @endforeach
        </select>
        <select wire:model.defer="condition" class="border rounded px-2 py-1">
            <option value="">Condition</option>
            @foreach($conditions as $cond)
                <option value="{{ $cond->value }}">{{ $cond->value }}</option>
            @endforeach
        </select>
        <input type="number" wire:model.defer="price" placeholder="Price" class="border rounded px-2 py-1" step="0.01" min="0" />
        <input type="text" wire:model.defer="sku" placeholder="SKU" class="border rounded px-2 py-1" />
        <input type="number" wire:model.defer="stock" placeholder="Stock" class="border rounded px-2 py-1" min="0" />
        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded col-span-1">{{ $editingId ? 'Update' : 'Add' }}</button>
        @if($editingId)
            <button type="button" wire:click="$set('editingId', null)" class="ml-2 px-2 py-1 border rounded col-span-1">Cancel</button>
        @endif
    </form>
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">ID</th>
                <th class="p-2 text-left">Card</th>
                <th class="p-2 text-left">Condition</th>
                <th class="p-2 text-left">Price</th>
                <th class="p-2 text-left">SKU</th>
                <th class="p-2 text-left">Stock</th>
                <th class="p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td class="p-2">{{ $product->id }}</td>
                    <td class="p-2">{{ $product->card->name ?? '' }}</td>
                    <td class="p-2">{{ $product->condition->value ?? '' }}</td>
                    <td class="p-2">{{ $product->price }}</td>
                    <td class="p-2">{{ $product->sku }}</td>
                    <td class="p-2">{{ $product->inventory->stock ?? 0 }}</td>
                    <td class="p-2">
                        <button wire:click="edit({{ $product->id }})" class="text-blue-600">Edit</button>
                        <button wire:click="delete({{ $product->id }})" class="text-red-600 ml-2">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 