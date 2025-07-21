<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Sets</h1>
    <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="mb-4 flex gap-2">
        <input type="text" wire:model.defer="name" placeholder="Set Name" class="border rounded px-2 py-1" />
        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">{{ $editingId ? 'Update' : 'Add' }}</button>
        @if($editingId)
            <button type="button" wire:click="$set('editingId', null)" class="ml-2 px-2 py-1 border rounded">Cancel</button>
        @endif
    </form>
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">ID</th>
                <th class="p-2 text-left">Name</th>
                <th class="p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sets as $set)
                <tr>
                    <td class="p-2">{{ $set->id }}</td>
                    <td class="p-2">{{ $set->name }}</td>
                    <td class="p-2">
                        <button wire:click="edit({{ $set->id }})" class="text-blue-600">Edit</button>
                        <button wire:click="delete({{ $set->id }})" class="text-red-600 ml-2">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 