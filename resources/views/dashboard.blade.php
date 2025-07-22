<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">
                                Welcome back, {{ Auth::user()->name }}! 🎉
                            </h3>
                            <p class="text-gray-600">
                                You're successfully logged into Konibui TCG Platform
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Account Information</h4>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Account Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if(Auth::user()->email_verified_at)
                                    ✅ Verified on {{ Auth::user()->email_verified_at->format('M d, Y') }}
                                @else
                                    ❌ Not verified
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">User Roles</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if(Auth::user()->roles->count() > 0)
                                    @foreach(Auth::user()->roles as $role)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-500">No roles assigned</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role Status</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if(Auth::user()->hasRole('Admin'))
                                    👑 <span class="text-red-600 font-semibold">Administrator</span>
                                @elseif(Auth::user()->hasRole('Employee'))
                                    👨‍💼 <span class="text-blue-600 font-semibold">Employee</span>
                                @elseif(Auth::user()->hasRole('Customer'))
                                    🛍️ <span class="text-green-600 font-semibold">Customer</span>
                                @else
                                    ❓ <span class="text-gray-500">No role assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-800 mb-4">Quick Actions</h4>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <a href="{{ route('profile') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                            <div class="text-blue-600 text-2xl mb-2">👤</div>
                            <h5 class="font-medium text-gray-800">Profile</h5>
                            <p class="text-sm text-gray-600">Update your account details</p>
                        </a>
                        
                        <a href="{{ route('test.defense-in-depth') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition-colors">
                            <div class="text-purple-600 text-2xl mb-2">🛡️</div>
                            <h5 class="font-medium text-gray-800">Security Tests</h5>
                            <p class="text-sm text-gray-600">Test Defense-in-Depth RBAC</p>
                        </a>
                        
                        <a href="{{ route('orders.index') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition-colors">
                            <div class="text-green-600 text-2xl mb-2">📊</div>
                            <h5 class="font-medium text-gray-800">Orders</h5>
                            <p class="text-sm text-gray-600">View your order history</p>
                        </a>

                        <!-- Admin Quick Actions -->
                        @if(Auth::user()->hasRole('Admin'))
                            <a href="{{ route('admin.dashboard') }}" class="bg-red-50 hover:bg-red-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-red-600 text-2xl mb-2">⚙️</div>
                                <h5 class="font-medium text-gray-800">Admin Dashboard</h5>
                                <p class="text-sm text-gray-600">System management</p>
                            </a>
                            
                            <a href="{{ route('admin.sets') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-yellow-600 text-2xl mb-2">🗂️</div>
                                <h5 class="font-medium text-gray-800">Manage Sets</h5>
                                <p class="text-sm text-gray-600">TCG set management</p>
                            </a>
                            
                            <a href="{{ route('admin.cards') }}" class="bg-indigo-50 hover:bg-indigo-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-indigo-600 text-2xl mb-2">🃏</div>
                                <h5 class="font-medium text-gray-800">Manage Cards</h5>
                                <p class="text-sm text-gray-600">Pokémon card catalog</p>
                            </a>
                            
                            <a href="{{ route('admin.products') }}" class="bg-orange-50 hover:bg-orange-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-orange-600 text-2xl mb-2">📦</div>
                                <h5 class="font-medium text-gray-800">Manage Products</h5>
                                <p class="text-sm text-gray-600">Product variants & inventory</p>
                            </a>
                        @elseif(Auth::user()->hasRole('Employee'))
                            <a href="{{ route('admin.dashboard') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-blue-600 text-2xl mb-2">👨‍💼</div>
                                <h5 class="font-medium text-gray-800">Staff Dashboard</h5>
                                <p class="text-sm text-gray-600">Employee tools</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h4 class="text-lg font-medium text-blue-800 mb-4">🎉 Platform Status</h4>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-medium text-blue-700 mb-2">✅ System Features</h5>
                        <ul class="text-sm text-blue-600 space-y-1">
                            <li>• User Authentication & Authorization</li>
                            <li>• Role-Based Access Control (RBAC)</li>
                            <li>• TCG Product & Inventory Management</li>
                            <li>• Admin Interface (EPIC 3)</li>
                            <li>• Defense-in-Depth Security</li>
                            <li>• Clean & Consistent UI</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h5 class="font-medium text-blue-700 mb-2">📋 Available Functions</h5>
                        <ul class="text-sm text-blue-600 space-y-1">
                            <li>• Profile Management</li>
                            <li>• Password Security</li>
                            <li>• Email Verification</li>
                            <li>• Session Management</li>
                            <li>• CSRF Protection</li>
                            <li>• Secure Logout</li>
                        </ul>
                    </div>
                </div>
                
                @if(Auth::user()->hasRole('Admin'))
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-sm text-blue-700">
                            <strong>Admin Status:</strong> You have full system access including EPIC 3 TCG management features.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
