@extends('layouts.testing')

@section('content')
<!-- Products Grid -->
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Available Products</h2>
            <p class="text-gray-600 text-sm mt-1">Sample products for testing e-commerce functionality</p>
        </div>
        @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                Add Product
            </button>
        @endif
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <!-- Product Card 1 -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="aspect-square bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Sample Product A</h3>
            <p class="text-gray-600 text-sm mb-3">Description of the first sample product for testing purposes.</p>
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">$99.99</span>
                @auth
                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Add to Cart
                    </button>
                @else
                    <span class="text-gray-400 text-sm">Login to purchase</span>
                @endauth
            </div>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-500 text-xs">Edit</button>
                        @if(auth()->user()->hasRole('Admin'))
                            <button class="text-red-600 hover:text-red-500 text-xs">Delete</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Card 2 -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="aspect-square bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Sample Product B</h3>
            <p class="text-gray-600 text-sm mb-3">Description of the second sample product for testing purposes.</p>
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">$149.99</span>
                @auth
                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Add to Cart
                    </button>
                @else
                    <span class="text-gray-400 text-sm">Login to purchase</span>
                @endauth
            </div>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-500 text-xs">Edit</button>
                        @if(auth()->user()->hasRole('Admin'))
                            <button class="text-red-600 hover:text-red-500 text-xs">Delete</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Card 3 -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="aspect-square bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Sample Product C</h3>
            <p class="text-gray-600 text-sm mb-3">Description of the third sample product for testing purposes.</p>
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">$79.99</span>
                @auth
                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Add to Cart
                    </button>
                @else
                    <span class="text-gray-400 text-sm">Login to purchase</span>
                @endauth
            </div>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-500 text-xs">Edit</button>
                        @if(auth()->user()->hasRole('Admin'))
                            <button class="text-red-600 hover:text-red-500 text-xs">Delete</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Card 4 -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="aspect-square bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Sample Product D</h3>
            <p class="text-gray-600 text-sm mb-3">Description of the fourth sample product for testing purposes.</p>
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">$199.99</span>
                @auth
                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Add to Cart
                    </button>
                @else
                    <span class="text-gray-400 text-sm">Login to purchase</span>
                @endauth
            </div>
            @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee')))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-500 text-xs">Edit</button>
                        @if(auth()->user()->hasRole('Admin'))
                            <button class="text-red-600 hover:text-red-500 text-xs">Delete</button>
                        @endif
                    </div>
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
                @auth
                    {{ auth()->user()->name }}
                    @if(auth()->user()->roles->count() > 0)
                        ({{ auth()->user()->roles->pluck('name')->join(', ') }})
                    @endif
                @else
                    Not authenticated
                @endauth
            </p>
        </div>
        <div>
            <span class="font-medium text-gray-700">Product Actions:</span>
            <ul class="text-gray-600 mt-1">
                @auth
                    <li>✅ Can view products</li>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        <li>✅ Can add products</li>
                        <li>✅ Can edit products</li>
                    @else
                        <li>❌ Cannot add products</li>
                        <li>❌ Cannot edit products</li>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                        <li>✅ Can delete products</li>
                    @else
                        <li>❌ Cannot delete products</li>
                    @endif
                @else
                    <li>✅ Can view products</li>
                    <li>❌ Cannot manage products</li>
                @endauth
            </ul>
        </div>
        <div>
            <span class="font-medium text-gray-700">Shopping Actions:</span>
            <ul class="text-gray-600 mt-1">
                @auth
                    <li>✅ Can add to cart</li>
                    <li>✅ Can place orders</li>
                @else
                    <li>❌ Login required</li>
                @endauth
            </ul>
        </div>
    </div>
</div>
@endsection 