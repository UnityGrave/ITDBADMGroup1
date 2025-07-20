<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name') }} - Testing Interface</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen">
    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                
                <!-- Logo/Brand Area -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-blue-600 rounded"></div>
                        <span class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</span>
                    </a>
                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">Testing UI</span>
                </div>

                <!-- Main Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('products.index') }}" 
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150">
                        Products
                    </a>
                    
                    @auth
                        <a href="{{ route('orders.index') }}" 
                           class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150">
                            My Orders
                        </a>
                        
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150">
                                Admin Dashboard
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- User Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                        <!-- User Info -->
                        <div class="hidden sm:flex items-center space-x-2">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                @if(auth()->user()->roles->count() > 0)
                                    <div class="text-xs text-gray-500">
                                        {{ auth()->user()->roles->first()->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-1 p-1.5 text-gray-600 hover:text-gray-900 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700 transition-colors duration-150">
                            Register
                        </a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button class="md:hidden p-1.5 text-gray-600 hover:text-gray-900" 
                            onclick="toggleMobileMenu()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-3">
                <div class="space-y-2">
                    <a href="{{ route('products.index') }}" 
                       class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Products</a>
                    
                    @auth
                        <a href="{{ route('orders.index') }}" 
                           class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">My Orders</a>
                        
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Admin Dashboard</a>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="px-3 py-2 text-sm text-gray-500">{{ auth()->user()->name }}</div>
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        @if(isset($pageTitle))
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                @if(isset($pageDescription))
                    <p class="mt-1 text-sm text-gray-600">{{ $pageDescription }}</p>
                @endif
            </div>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500">
                <div class="mb-2 sm:mb-0">
                    <span class="font-medium">{{ config('app.name') }}</span> Testing Interface
                </div>
                <div class="flex items-center space-x-1">
                    <span>User:</span>
                    @auth
                        <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->roles->count() > 0)
                            <span class="text-gray-400">·</span>
                            <span class="font-medium text-blue-600">{{ auth()->user()->roles->pluck('name')->join(', ') }}</span>
                        @endif
                    @else
                        <span class="text-gray-400">Not authenticated</span>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for mobile menu -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
    
    <!-- Alpine.js for dropdowns -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html> 