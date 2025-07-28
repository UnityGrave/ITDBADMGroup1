<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Admin Dashboard Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">System Management</h2>
                        <p class="text-gray-600 text-sm mt-1">Administrative controls and system overview</p>
                    </div>
                    <div class="text-sm bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                        {{ auth()->user()->roles->first()->name ?? 'Admin' }} Access
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3.203a4.5 4.5 0 11-6.98 0"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ App\Models\User::count() }}</p>
                            <p class="text-xs text-green-600 mt-1">Active users</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">TCG Sets</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Set::count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">Available sets</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Cards</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Card::count() }}</p>
                            <p class="text-xs text-blue-600 mt-1">In catalog</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Products</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::count() }}</p>
                            <p class="text-xs text-green-600 mt-1">Variants available</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TCG Management Section -->
            @if(auth()->user()->hasRole('Admin'))
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Product Management</h3>
                    <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded">Fully Implemented</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.sets') }}" class="flex flex-col items-center p-4 bg-yellow-50 border border-yellow-200 rounded hover:bg-yellow-100 transition-colors">
                        <svg class="h-8 w-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="text-sm font-medium text-yellow-800">Manage Sets</span>
                        <span class="text-xs text-yellow-600">{{ \App\Models\Set::count() }} sets</span>
                    </a>

                    <a href="{{ route('admin.rarities') }}" class="flex flex-col items-center p-4 bg-pink-50 border border-pink-200 rounded hover:bg-pink-100 transition-colors">
                        <svg class="h-8 w-8 text-pink-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <span class="text-sm font-medium text-pink-800">Manage Rarities</span>
                        <span class="text-xs text-pink-600">{{ \App\Models\Rarity::count() }} rarities</span>
                    </a>

                    <a href="{{ route('admin.cards') }}" class="flex flex-col items-center p-4 bg-indigo-50 border border-indigo-200 rounded hover:bg-indigo-100 transition-colors">
                        <svg class="h-8 w-8 text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                        </svg>
                        <span class="text-sm font-medium text-indigo-800">Manage Cards</span>
                        <span class="text-xs text-indigo-600">{{ \App\Models\Card::count() }} cards</span>
                    </a>

                    <a href="{{ route('admin.products') }}" class="flex flex-col items-center p-4 bg-orange-50 border border-orange-200 rounded hover:bg-orange-100 transition-colors">
                        <svg class="h-8 w-8 text-orange-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="text-sm font-medium text-orange-800">Manage Products</span>
                        <span class="text-xs text-orange-600">{{ \App\Models\Product::count() }} products</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Management Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- User Management -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">User Management</h3>
                        @if(auth()->user()->hasRole('Admin'))
                            <button onclick="openAddUserModal()" class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700">
                                Add User
                            </button>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @foreach(App\Models\User::with('roles')->limit(5)->get() as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    <div class="flex space-x-1 mt-1">
                                        @forelse($user->roles as $role)
                                            <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-700 rounded">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded">
                                                No Role Assigned
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                                        <button onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->roles->pluck('name')->join(',') ?: 'Customer' }}')" class="text-blue-600 hover:text-blue-500 text-sm">Edit</button>
                                    @endif
                                    @if(auth()->user()->hasRole('Admin') && $user->id !== auth()->id())
                                        <button onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-500 text-sm">Delete</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Laravel Version</span>
                            <span class="font-medium">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Environment</span>
                            <span class="font-medium">{{ config('app.env') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Database</span>
                            <span class="font-medium">{{ config('database.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cache Driver</span>
                            <span class="font-medium">{{ config('cache.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">EPIC 3 Status</span>
                            <span class="text-green-600 font-medium">✅ Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <x-modal name="addUserModal" :show="false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Add New User</h2>
            
            <form id="addUserForm" onsubmit="addUser(event)">
                @csrf
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label for="addUserName" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="addUserName" name="name" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="addUserEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="addUserEmail" name="email" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="addUserPassword" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="addUserPassword" name="password" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>
                    
                    <div>
                        <label for="addUserPasswordConfirm" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="addUserPasswordConfirm" name="password_confirmation" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="addUserRole" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="addUserRole" name="role" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a role</option>
                            <option value="Customer">Customer</option>
                            <option value="Employee">Employee</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Edit User Modal -->
    <x-modal name="editUserModal" :show="false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Edit User</h2>
            
            <form id="editUserForm" onsubmit="updateUser(event)">
                @csrf
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label for="editUserName" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="editUserName" name="name" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="editUserEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editUserEmail" name="email" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="editUserPassword" class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                        <input type="password" id="editUserPassword" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password. Minimum 8 characters if changing.</p>
                    </div>
                    
                    <div>
                        <label for="editUserPasswordConfirm" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="editUserPasswordConfirm" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="editUserRole" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="editUserRole" name="role" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Customer">Customer</option>
                            <option value="Employee">Employee</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Delete User Confirmation Modal -->
    <x-modal name="deleteUserModal" :show="false">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 8.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Delete User</h3>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Are you sure you want to delete user <span id="deleteUserName" class="font-medium"></span>? This action cannot be undone.
                </p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" onclick="deleteUser()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Delete User
                </button>
            </div>
        </div>
    </x-modal>

    <script>
        let currentUserId = null;

        // Add User Modal Functions
        function openAddUserModal() {
            document.getElementById('addUserForm').reset();
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'addUserModal'
            }));
        }

        function closeAddUserModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'addUserModal'
            }));
        }

        // Edit User Modal Functions
        function openEditUserModal(userId, userName, userEmail, userRoles) {
            currentUserId = userId;
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').value = userName;
            document.getElementById('editUserEmail').value = userEmail;
            
            // Set the role (assuming single role for now)
            const roleSelect = document.getElementById('editUserRole');
            const roles = userRoles.split(',');
            if (roles.length > 0) {
                roleSelect.value = roles[0];
            }
            
            // Clear password fields
            document.getElementById('editUserPassword').value = '';
            document.getElementById('editUserPasswordConfirm').value = '';
            
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'editUserModal'
            }));
        }

        function closeEditUserModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'editUserModal'
            }));
            currentUserId = null;
        }

        // Delete User Modal Functions
        function confirmDeleteUser(userId, userName) {
            currentUserId = userId;
            document.getElementById('deleteUserName').textContent = userName;
            
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'deleteUserModal'
            }));
        }

        function closeDeleteUserModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'deleteUserModal'
            }));
            currentUserId = null;
        }

        // Add User Function
        async function addUser(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch('/admin/users', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    alert('User added successfully!');
                    window.location.reload();
                } else {
                    let errorMessage = 'Error: ' + (result.message || 'Unknown error occurred');
                    if (result.errors) {
                        const errorDetails = Object.values(result.errors).flat().join('\n');
                        errorMessage += '\n\nValidation errors:\n' + errorDetails;
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                alert('An error occurred while adding the user: ' + error.message);
                console.error('Error:', error);
            }
            
            closeAddUserModal();
        }

        // Update User Function
        async function updateUser(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            // Add method override for PUT request
            formData.append('_method', 'PUT');
            
            try {
                const response = await fetch(`/admin/users/${currentUserId}`, {
                    method: 'POST', // Use POST with method override
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    alert('User updated successfully!');
                    window.location.reload();
                } else {
                    let errorMessage = 'Error: ' + (result.message || 'Unknown error occurred');
                    if (result.errors) {
                        const errorDetails = Object.values(result.errors).flat().join('\n');
                        errorMessage += '\n\nValidation errors:\n' + errorDetails;
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                alert('An error occurred while updating the user: ' + error.message);
                console.error('Error:', error);
            }
            
            closeEditUserModal();
        }

        // Delete User Function
        async function deleteUser() {
            if (!currentUserId) return;
            
            try {
                const response = await fetch(`/admin/users/${currentUserId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User deleted successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred while deleting the user.');
                console.error('Error:', error);
            }
            
            closeDeleteUserModal();
        }
    </script>
</x-app-layout>
