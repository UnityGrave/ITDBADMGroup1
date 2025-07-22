<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-pokemon-red/5 via-white to-konbini-green/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-16 md:py-24">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h1 class="text-4xl sm:text-5xl font-display font-extrabold text-pokemon-black mb-4">
                            Your Trusted Source for Pokémon Cards
                        </h1>
                        <p class="text-lg text-brand-gray-600 mb-8">
                            Discover an extensive collection of Pokémon Trading Card Game products in Konibui! From booster packs to single cards, we've got everything you need!
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('products.index') }}" 
                                class="inline-flex items-center px-6 py-3 bg-pokemon-red text-white font-display font-semibold rounded-lg shadow-lg hover:bg-red-600 transition-colors duration-200">
                                Shop Now
                                <svg class="w-5 h-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ route('register') }}" 
                                class="inline-flex items-center px-6 py-3 bg-white text-pokemon-black font-display font-semibold rounded-lg shadow-lg hover:bg-brand-gray-50 transition-colors duration-200">
                                Create Account
                            </a>
                        </div>
                    </div>
                    <div class="relative hidden md:block">
                        <div class="absolute inset-0 bg-gradient-to-br from-pokemon-red/20 to-konbini-green/20 rounded-lg transform rotate-3"></div>
                        <img src="https://assets.pokemon.com/static-assets/content-assets/cms2/img/cards/web/SV3PT5/SV3PT5_EN_133.png" 
                             alt="Featured Pokémon Card" 
                             class="relative rounded-lg shadow-xl transform -rotate-3 transition-transform duration-300 hover:rotate-0"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Shortcuts -->
    <div class="py-16 bg-brand-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-display font-bold text-pokemon-black text-center mb-12">Shop by Category</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Single Cards -->
                <a href="{{ route('products.index', ['category' => 'Single Card']) }}" 
                    class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="aspect-[4/3] bg-gradient-to-br from-pokemon-red/10 to-konbini-green/10">
                        <div class="absolute inset-0 flex items-center justify-center">
                        <!-- insert -->
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-display font-bold text-lg text-pokemon-black group-hover:text-pokemon-red transition-colors duration-200">Single Cards</h3>
                        <p class="text-brand-gray-600 text-sm">Find your favorite Pokémon cards</p>
                    </div>
                </a>

                <!-- Booster Packs -->
                <a href="{{ route('products.index', ['category' => 'Booster Pack']) }}" 
                    class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="aspect-[4/3] bg-gradient-to-br from-konbini-orange/10 to-pokemon-red/10">
                        <div class="absolute inset-0 flex items-center justify-center">
                        <!-- insert -->
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-display font-bold text-lg text-pokemon-black group-hover:text-konbini-orange transition-colors duration-200">Booster Packs</h3>
                        <p class="text-brand-gray-600 text-sm">Discover new cards and surprises</p>
                    </div>
                </a>

                <!-- Boxes -->
                <a href="{{ route('products.index', ['category' => 'Box']) }}" 
                    class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="aspect-[4/3] bg-gradient-to-br from-konbini-green/10 to-konbini-orange/10">
                        <div class="absolute inset-0 flex items-center justify-center">
                        <!-- insert -->
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-display font-bold text-lg text-pokemon-black group-hover:text-konbini-green transition-colors duration-200">Boxes</h3>
                        <p class="text-brand-gray-600 text-sm">Complete collections and special sets</p>
                    </div>
                </a>

                <!-- Accessories -->
                <a href="{{ route('products.index', ['category' => 'Box']) }}" 
                    class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="aspect-[4/3] bg-gradient-to-br from-brand-gray-200 to-brand-gray-100">
                        <div class="absolute inset-0 flex items-center justify-center">
                        <!-- insert -->
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-display font-bold text-lg text-pokemon-black group-hover:text-brand-gray-700 transition-colors duration-200">Accessories</h3>
                        <p class="text-brand-gray-600 text-sm">Sleeves, deck boxes, and more</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Authentic Products -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-pokemon-red/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-pokemon-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-pokemon-black mb-2">100% Authentic</h3>
                    <p class="text-brand-gray-600">All our products are genuine and sourced directly from official distributors</p>
                </div>

                <!-- Fast Shipping -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-konbini-green/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-konbini-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-pokemon-black mb-2">Fast Shipping</h3>
                    <p class="text-brand-gray-600">Quick and secure delivery with tracking on all orders</p>
                </div>

                <!-- Expert Support -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-konbini-orange/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-konbini-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-pokemon-black mb-2">Expert Support</h3>
                    <p class="text-brand-gray-600">Our knowledgeable team is here to help with any questions</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
