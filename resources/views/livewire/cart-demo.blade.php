{{-- Cart Demo Page --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Shopping Cart Demo</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Test the shopping cart functionality by adding products to your trainer's bag. 
                The cart supports both authenticated users (database storage) and guests (session storage).
            </p>
        </div>

        {{-- Demo Instructions --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">How to Test</h3>
                    <ul class="space-y-1 text-blue-800">
                        <li>• Click "Add to Bag" or "Quick Add" on any product card</li>
                        <li>• Click the cart icon in the navigation to view your bag</li>
                        <li>• Update quantities or remove items from the cart panel</li>
                        <li>• Cart badge shows total item count</li>
                        <li>• Cart persists across page reloads (database for users, session for guests)</li>
                    </ul>
                </div>
            </div>
        </div>

        @if($products->count() > 0)
            {{-- Products Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($products as $product)
                    <livewire:product-card :product="$product" :key="'product-'.$product->id" />
                @endforeach
            </div>
        @else
            {{-- No Products State --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m6-8v2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Products Available</h3>
                <p class="mt-2 text-gray-500">Please seed the database with products to test the cart functionality.</p>
                
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left max-w-md mx-auto">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">To add products:</h4>
                    <code class="text-xs text-yellow-700 bg-yellow-100 px-2 py-1 rounded block">
                        docker-compose exec app php artisan db:seed --class=ProductSeeder
                    </code>
                </div>
            </div>
        @endif

        {{-- Features Showcase --}}
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Cart Features</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="text-center">
                    <div class="bg-green-100 rounded-full p-3 w-12 h-12 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Dual Storage</h3>
                    <p class="text-gray-600 text-sm">Database storage for users, session storage for guests</p>
                </div>

                {{-- Feature 2 --}}
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-3 w-12 h-12 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-time Updates</h3>
                    <p class="text-gray-600 text-sm">Instant cart updates with Livewire events</p>
                </div>

                {{-- Feature 3 --}}
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full p-3 w-12 h-12 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Smooth UX</h3>
                    <p class="text-gray-600 text-sm">Alpine.js powered slide-out cart panel</p>
                </div>
            </div>
        </div>
    </div>
</div>
