<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                Welcome back, {{ Auth::user()->name }}! 🎉
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                You're successfully logged into Konibui E-commerce Platform
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Account Information</h4>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Created</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Verified</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if(Auth::user()->email_verified_at)
                                    ✅ Verified on {{ Auth::user()->email_verified_at->format('M d, Y') }}
                                @else
                                    ❌ Not verified
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Roles</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Status</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Quick Actions</h4>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <a href="{{ route('profile') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                            <div class="text-blue-600 text-2xl mb-2">👤</div>
                            <h5 class="font-medium text-gray-800">Profile</h5>
                            <p class="text-sm text-gray-600">Update your account details</p>
                        </a>
                        
                        <a href="{{ route('test.defense-in-depth') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition-colors">
                            <div class="text-purple-600 text-2xl mb-2">🛡️</div>
                            <h5 class="font-medium text-gray-800">Defense-in-Depth Security</h5>
                            <p class="text-sm text-gray-600">Test Gatekeeper + Vault RBAC</p>
                        </a>
                        
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-green-600 text-2xl mb-2">📊</div>
                            <h5 class="font-medium text-gray-800">Orders</h5>
                            <p class="text-sm text-gray-600">View order history (Coming Soon)</p>
                        </div>

                        <!-- Admin Quick Actions -->
                        @if(Auth::user()->hasRole('Admin'))
                            <a href="{{ route('admin.sets') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-yellow-600 text-2xl mb-2">🗂️</div>
                                <h5 class="font-medium text-gray-800">Manage Sets</h5>
                                <p class="text-sm text-gray-600">Add or edit TCG sets</p>
                            </a>
                            <a href="{{ route('admin.rarities') }}" class="bg-pink-50 hover:bg-pink-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-pink-600 text-2xl mb-2">💎</div>
                                <h5 class="font-medium text-gray-800">Manage Rarities</h5>
                                <p class="text-sm text-gray-600">Add or edit card rarities</p>
                            </a>
                            <a href="{{ route('admin.cards') }}" class="bg-indigo-50 hover:bg-indigo-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-indigo-600 text-2xl mb-2">🃏</div>
                                <h5 class="font-medium text-gray-800">Manage Cards</h5>
                                <p class="text-sm text-gray-600">Add or edit Pokémon cards</p>
                            </a>
                            <a href="{{ route('admin.products') }}" class="bg-orange-50 hover:bg-orange-100 rounded-lg p-4 text-center transition-colors">
                                <div class="text-orange-600 text-2xl mb-2">📦</div>
                                <h5 class="font-medium text-gray-800">Manage Products</h5>
                                <p class="text-sm text-gray-600">Manage product variants & inventory</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Testing Information -->
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6">
                <h4 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-4">🧪 Authentication Testing Status</h4>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-medium text-blue-700 dark:text-blue-300 mb-2">✅ Completed Tests</h5>
                        <ul class="text-sm text-blue-600 dark:text-blue-400 space-y-1">
                            <li>• User Registration</li>
                            <li>• Email Validation</li>
                            <li>• Password Hashing</li>
                            <li>• Auto-login after Registration</li>
                            <li>• Session Management</li>
                            <li>• Authentication Middleware</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h5 class="font-medium text-blue-700 dark:text-blue-300 mb-2">📋 Available Functions</h5>
                        <ul class="text-sm text-blue-600 dark:text-blue-400 space-y-1">
                            <li>• Profile Updates</li>
                            <li>• Password Changes</li>
                            <li>• Email Verification</li>
                            <li>• Secure Logout</li>
                            <li>• Password Reset</li>
                            <li>• CSRF Protection</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
