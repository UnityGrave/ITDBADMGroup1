<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Cards</h1>
    <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="mb-4 grid grid-cols-5 gap-2">
        <input type="text" wire:model.defer="name" placeholder="Card Name" class="border rounded px-2 py-1" />
        <input type="text" wire:model.defer="collector_number" placeholder="Collector #" class="border rounded px-2 py-1" />
        <select wire:model.defer="set_id" class="border rounded px-2 py-1">
            <option value="">Set</option>
            @foreach($sets as $set)
                <option value="{{ $set->id }}">{{ $set->name }}</option>
            @endforeach
        </select>
        <select wire:model.defer="rarity_id" class="border rounded px-2 py-1">
            <option value="">Rarity</option>
            @foreach($rarities as $rarity)
                <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
            @endforeach
        </select>
        <select wire:model.defer="category_id" class="border rounded px-2 py-1">
            <option value="">Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded col-span-1">{{ $editingId ? 'Update' : 'Add' }}</button>
        @if($editingId)
            <button type="button" wire:click="$set('editingId', null)" class="ml-2 px-2 py-1 border rounded col-span-1">Cancel</button>
        @endif
    </form>
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">ID</th>
                <th class="p-2 text-left">Name</th>
                <th class="p-2 text-left">Collector #</th>
                <th class="p-2 text-left">Set</th>
                <th class="p-2 text-left">Rarity</th>
                <th class="p-2 text-left">Category</th>
                <th class="p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cards as $card)
                <tr>
                    <td class="p-2">{{ $card->id }}</td>
                    <td class="p-2">{{ $card->name }}</td>
                    <td class="p-2">{{ $card->collector_number }}</td>
                    <td class="p-2">{{ $card->set->name ?? '' }}</td>
                    <td class="p-2">{{ $card->rarity->name ?? '' }}</td>
                    <td class="p-2">{{ $card->category->name ?? '' }}</td>
                    <td class="p-2">
                        <button wire:click="edit({{ $card->id }})" class="text-blue-600">Edit</button>
                        <button wire:click="delete({{ $card->id }})" class="text-red-600 ml-2">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 