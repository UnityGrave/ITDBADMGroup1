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
                    <h3 class="text-lg font-semibold text-gray-900">TCG Management (EPIC 3)</h3>
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
                            <button class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700">
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
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-700 rounded">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                                        <button class="text-blue-600 hover:text-blue-500 text-sm">Edit</button>
                                    @endif
                                    @if(auth()->user()->hasRole('Admin'))
                                        <button class="text-red-600 hover:text-red-500 text-sm">Delete</button>
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

            <!-- RBAC Information -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role-Based Access Control</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Current User:</span>
                        <p class="text-gray-600">
                            {{ auth()->user()->name }}
                            @if(auth()->user()->roles->count() > 0)
                                ({{ auth()->user()->roles->pluck('name')->join(', ') }})
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Admin Permissions:</span>
                        <ul class="text-gray-600 mt-1">
                            @if(auth()->user()->hasRole('Admin'))
                                <li>✅ Full system access</li>
                                <li>✅ TCG management</li>
                                <li>✅ User management</li>
                                <li>✅ All CRUD operations</li>
                            @elseif(auth()->user()->hasRole('Employee'))
                                <li>✅ Limited admin access</li>
                                <li>✅ View dashboards</li>
                                <li>❌ No TCG management</li>
                                <li>❌ Limited user access</li>
                            @else
                                <li>❌ No admin access</li>
                            @endif
                        </ul>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">System Features:</span>
                        <ul class="text-gray-600 mt-1">
                            <li>✅ EPIC 3 TCG System</li>
                            <li>✅ Clean UI Design</li>
                            <li>✅ Defense-in-Depth</li>
                            <li>✅ Role-based Security</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 