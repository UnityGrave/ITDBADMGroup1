<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">TCG Rarities Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Add, edit, and manage card rarities</p>
                </div>
                <div class="text-sm bg-pink-100 text-pink-800 px-3 py-1 rounded-full">
                    {{ $rarities->count() }} Rarities
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Rarity' : 'Add New Rarity' }}
                </h3>
                
                <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Rarity Name</label>
                            <input type="text" 
                                   wire:model.defer="name" 
                                   id="name"
                                   placeholder="e.g., Common, Rare, Ultra Rare..." 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500" />
                            @error('name') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        @if($editingId)
                            <button type="button" 
                                    wire:click="$set('editingId', null)" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                Cancel
                            </button>
                        @endif
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                            {{ $editingId ? 'Update Rarity' : 'Add Rarity' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rarities List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Rarities</h3>
                
                @if($rarities->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rarity Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rarities as $rarity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $rarity->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $rarity->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $rarity->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $rarity->id }})" 
                                                    class="text-pink-600 hover:text-pink-900 mr-3">
                                                Edit
                                            </button>
                                            <button wire:click="delete({{ $rarity->id }})" 
                                                    onclick="return confirm('Are you sure you want to delete this rarity?')"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No rarities available</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first rarity type.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 