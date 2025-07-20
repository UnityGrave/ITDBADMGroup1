@extends('layouts.testing')

@section('content')
<!-- Hero Section -->
<div class="text-center mb-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-3">Application Testing Interface</h1>
    <p class="text-base text-gray-600 max-w-2xl mx-auto leading-relaxed">
        Clean, minimalist UI for testing core application functionality including authentication, 
        role-based access control, and business logic.
    </p>
</div>

<!-- Authentication Status Card -->
<div class="max-w-3xl mx-auto mb-10">
    @auth
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Authenticated</h3>
                        <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach(auth()->user()->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">Account Status</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ auth()->user()->email_verified_at ? 'Email verified' : 'Email not verified' }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Not Authenticated</h3>
                <p class="text-gray-600 mb-6">Please login or register to access full functionality</p>
                <div class="flex justify-center space-x-3">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-150">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors duration-150">
                        Register
                    </a>
                </div>
            </div>
        </div>
    @endauth
</div>

<!-- Quick Actions Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    
    <!-- Products Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors duration-150">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-base font-semibold text-gray-900">Products</h3>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">Browse and manage product catalog</p>
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-150">
            View Products
            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <!-- Orders Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors duration-150">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-base font-semibold text-gray-900">Orders</h3>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">View order history and status</p>
        @auth
            <a href="{{ route('orders.index') }}" 
               class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-500 transition-colors duration-150">
                My Orders
                <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="text-sm text-gray-400">Login required</span>
        @endauth
    </div>

    <!-- Admin Dashboard -->
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors duration-150">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-base font-semibold text-gray-900">Admin Dashboard</h3>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">Manage users, orders, and system settings</p>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-500 transition-colors duration-150">
                Open Dashboard
                <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    @endif

    <!-- Profile/Authentication -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors duration-150">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-base font-semibold text-gray-900">Profile</h3>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">Manage account settings and preferences</p>
        @auth
            <a href="{{ route('profile.edit') }}" 
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-500 transition-colors duration-150">
                Edit Profile
                <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <a href="{{ route('login') }}" 
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-500 transition-colors duration-150">
                Login to Access
                <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endauth
    </div>
</div>

<!-- System Information -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
    <h3 class="text-base font-semibold text-gray-900 mb-4">System Information</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Laravel Version</span>
            <span class="mt-1 text-sm font-medium text-gray-900">{{ app()->version() }}</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Environment</span>
            <span class="mt-1 text-sm font-medium text-gray-900">{{ config('app.env') }}</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Database</span>
            <span class="mt-1 text-sm font-medium text-gray-900">{{ config('database.default') }}</span>
        </div>
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Users Count</span>
            <span class="mt-1 text-sm font-medium text-gray-900">{{ App\Models\User::count() }}</span>
        </div>
    </div>
</div>

<!-- Testing Links -->
@auth
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">RBAC Testing Links</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('test.authenticated') }}" 
               class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-150">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center">
                        <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-900">Authenticated Area</span>
                        <p class="text-xs text-gray-500">Any logged-in user</p>
                    </div>
                </div>
            </a>
            
            @if(auth()->user()->hasRole('Customer'))
                <a href="{{ route('test.customer') }}" 
                   class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-150">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-green-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-green-700">Customer Area</span>
                            <p class="text-xs text-gray-500">Customer role required</p>
                        </div>
                    </div>
                </a>
            @endif
            
            @if(auth()->user()->hasRole('Employee') || auth()->user()->hasRole('Admin'))
                <a href="{{ route('test.staff') }}" 
                   class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-150">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Staff Area</span>
                            <p class="text-xs text-gray-500">Employee/Admin role required</p>
                        </div>
                    </div>
                </a>
            @endif
            
            @if(auth()->user()->hasRole('Admin'))
                <a href="{{ route('test.admin') }}" 
                   class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-150">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-red-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-red-700">Admin Only</span>
                            <p class="text-xs text-gray-500">Admin role required</p>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>
@endauth
@endsection
