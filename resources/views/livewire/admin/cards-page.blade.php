<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">TCG Cards Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Add, edit, and manage Pokémon cards</p>
                </div>
                <div class="text-sm bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">
                    {{ $cards->count() }} Cards
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Card' : 'Add New Card' }}
                </h3>
                
                <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Card Name</label>
                            <input type="text" 
                                   wire:model.defer="name" 
                                   id="name"
                                   placeholder="e.g., Charizard, Pikachu..." 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                            @error('name') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label for="collector_number" class="block text-sm font-medium text-gray-700">Collector Number</label>
                            <input type="text" 
                                   wire:model.defer="collector_number" 
                                   id="collector_number"
                                   placeholder="e.g., 006/102" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                            @error('collector_number') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label for="set_id" class="block text-sm font-medium text-gray-700">Set</label>
                            <select wire:model.defer="set_id" 
                                    id="set_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a set</option>
                                @foreach($sets as $set)
                                    <option value="{{ $set->id }}">{{ $set->name }}</option>
                                @endforeach
                            </select>
                            @error('set_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label for="rarity_id" class="block text-sm font-medium text-gray-700">Rarity</label>
                            <select wire:model.defer="rarity_id" 
                                    id="rarity_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a rarity</option>
                                @foreach($rarities as $rarity)
                                    <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
                                @endforeach
                            </select>
                            @error('rarity_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                            <select wire:model.defer="category_id" 
                                    id="category_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        @if($editingId)
                            <button type="button" 
                                    wire:click="$set('editingId', null)" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                        @endif
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $editingId ? 'Update Card' : 'Add Card' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Cards</h3>
                
                @if($cards->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Set</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rarity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cards as $card)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $card->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $card->collector_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $card->set->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $card->rarity->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $card->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $card->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </button>
                                            <button wire:click="delete({{ $card->id }})" 
                                                    onclick="return confirm('Are you sure you want to delete this card?')"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No cards available</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first card.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 