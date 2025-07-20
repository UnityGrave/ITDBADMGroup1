@extends('layouts.testing')

@section('content')
<!-- Admin Dashboard Header -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Admin Dashboard</h2>
            <p class="text-gray-600 text-sm mt-1">System management and administrative controls</p>
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
                <h3 class="text-sm font-medium text-gray-500">Products</h3>
                <p class="text-2xl font-bold text-gray-900">4</p>
                <p class="text-xs text-gray-600 mt-1">Sample products</p>
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
                <h3 class="text-sm font-medium text-gray-500">Orders</h3>
                <p class="text-2xl font-bold text-gray-900">23</p>
                <p class="text-xs text-blue-600 mt-1">This month</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="bg-purple-100 p-2 rounded-lg">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Revenue</h3>
                <p class="text-2xl font-bold text-gray-900">$3,247</p>
                <p class="text-xs text-green-600 mt-1">+12% from last month</p>
            </div>
        </div>
    </div>
</div>

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
        
        <div class="mt-4 text-center">
            <a href="#" class="text-blue-600 hover:text-blue-500 text-sm">View All Users →</a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-500 text-sm">View All</a>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium text-gray-900">#ORD-003</p>
                    <p class="text-sm text-gray-500">Sample Product D - $199.99</p>
                    <p class="text-xs text-gray-400">{{ now()->format('M j, Y g:i A') }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Pending</span>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        <div class="mt-1">
                            <button class="text-green-600 hover:text-green-500 text-xs">Process</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium text-gray-900">#ORD-002</p>
                    <p class="text-sm text-gray-500">Sample Product C - $79.99</p>
                    <p class="text-xs text-gray-400">{{ now()->subDays(1)->format('M j, Y g:i A') }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        <div class="mt-1">
                            <button class="text-blue-600 hover:text-blue-500 text-xs">Ship</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium text-gray-900">#ORD-001</p>
                    <p class="text-sm text-gray-500">Sample Product A & B - $249.98</p>
                    <p class="text-xs text-gray-400">{{ now()->subDays(3)->format('M j, Y g:i A') }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Delivered</span>
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="mt-1">
                            <button class="text-purple-600 hover:text-purple-500 text-xs">Archive</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information & Settings -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- System Info -->
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
                <span class="text-gray-600">Mail Driver</span>
                <span class="font-medium">{{ config('mail.default') }}</span>
            </div>
        </div>
        
        @if(auth()->user()->hasRole('Admin'))
            <div class="mt-6 pt-4 border-t border-gray-200">
                <button class="w-full bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 text-sm">
                    System Settings
                </button>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        
        <div class="grid grid-cols-2 gap-4">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                <button class="flex flex-col items-center p-4 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition-colors">
                    <svg class="h-6 w-6 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Add Product</span>
                </button>
                
                <button class="flex flex-col items-center p-4 bg-green-50 border border-green-200 rounded hover:bg-green-100 transition-colors">
                    <svg class="h-6 w-6 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800">Process Orders</span>
                </button>
            @endif
            
            @if(auth()->user()->hasRole('Admin'))
                <button class="flex flex-col items-center p-4 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100 transition-colors">
                    <svg class="h-6 w-6 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3.203a4.5 4.5 0 11-6.98 0"/>
                    </svg>
                    <span class="text-sm font-medium text-purple-800">Manage Users</span>
                </button>
                
                <button class="flex flex-col items-center p-4 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors">
                    <svg class="h-6 w-6 text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800">Analytics</span>
                </button>
            @else
                <div class="col-span-2 text-center py-4 text-gray-500 text-sm">
                    Additional admin actions available to Admin role only
                </div>
            @endif
        </div>
    </div>
</div>

<!-- RBAC Testing Information -->
<div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Role-Based Access Control Test</h3>
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
                    <li>✅ User management</li>
                    <li>✅ Delete operations</li>
                    <li>✅ System settings</li>
                @elseif(auth()->user()->hasRole('Employee'))
                    <li>✅ Limited admin access</li>
                    <li>✅ Order management</li>
                    <li>❌ Cannot delete users</li>
                    <li>❌ No system settings</li>
                @else
                    <li>❌ No admin access</li>
                @endif
            </ul>
        </div>
        <div>
            <span class="font-medium text-gray-700">Dashboard Access:</span>
            <ul class="text-gray-600 mt-1">
                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                    <li>✅ Can access dashboard</li>
                    <li>✅ Can view statistics</li>
                    <li>✅ Can manage content</li>
                @else
                    <li>❌ Dashboard restricted</li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection 