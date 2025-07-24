{{-- Multi-Step Checkout Page --}}
<div class="min-h-screen bg-gray-50 py-4">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
            <p class="text-gray-600">Complete your order in a few simple steps</p>
        </div>

        {{-- Progress Bar --}}
        <div class="mb-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $this->step === 'shipping' ? 'bg-pokemon-red text-white' : ($this->isStepCompleted('shipping') ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                        @if($this->isStepCompleted('shipping'))
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $this->step === 'shipping' ? 'text-pokemon-red' : ($this->isStepCompleted('shipping') ? 'text-green-600' : 'text-gray-500') }}">
                        Shipping
                    </span>
                </div>
                
                <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $this->step === 'payment' ? 'bg-pokemon-red text-white' : ($this->isStepCompleted('payment') ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                        @if($this->isStepCompleted('payment'))
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            2
                        @endif
                    </div>
                                            <span class="ml-2 text-sm font-medium {{ $this->step === 'payment' ? 'text-pokemon-red' : ($this->isStepCompleted('payment') ? 'text-green-600' : 'text-gray-500') }}">
                        Payment
                    </span>
                </div>
                
                <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $this->step === 'review' ? 'bg-pokemon-red text-white' : 'bg-gray-300 text-gray-600' }}">
                        3
                    </div>
                                            <span class="ml-2 text-sm font-medium {{ $this->step === 'review' ? 'text-pokemon-red' : 'text-gray-500' }}">
                        Review
                    </span>
                </div>
            </div>
            
            {{-- Progress Line --}}
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-pokemon-red h-2 rounded-full transition-all duration-300" style="width: {{ $this->getProgressPercentage() }}%"></div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            
            {{-- STEP 1: Shipping Information --}}
            @if($this->step === 'shipping')
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Information</h2>
                    
                    <form wire:submit.prevent="nextStep" class="space-y-6">
                        {{-- Name Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    First Name *
                                </label>
                                <input 
                                    type="text" 
                                    id="shipping_first_name"
                                    wire:model.defer="shipping_first_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="Enter your first name"
                                />
                                @error('shipping_first_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Name *
                                </label>
                                <input 
                                    type="text" 
                                    id="shipping_last_name"
                                    wire:model.defer="shipping_last_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="Enter your last name"
                                />
                                @error('shipping_last_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Contact Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Address *
                                </label>
                                <input 
                                    type="email" 
                                    id="shipping_email"
                                    wire:model.defer="shipping_email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="Enter your email"
                                />
                                @error('shipping_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number *
                                </label>
                                <input 
                                    type="tel" 
                                    id="shipping_phone"
                                    wire:model.defer="shipping_phone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="(555) 123-4567"
                                />
                                @error('shipping_phone')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Address Fields --}}
                        <div>
                            <label for="shipping_address_line_1" class="block text-sm font-medium text-gray-700 mb-1">
                                Street Address *
                            </label>
                            <input 
                                type="text" 
                                id="shipping_address_line_1"
                                wire:model.defer="shipping_address_line_1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                placeholder="123 Main Street"
                            />
                            @error('shipping_address_line_1')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address_line_2" class="block text-sm font-medium text-gray-700 mb-1">
                                Apartment, Suite, etc. (Optional)
                            </label>
                            <input 
                                type="text" 
                                id="shipping_address_line_2"
                                wire:model.defer="shipping_address_line_2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                placeholder="Apt 4B"
                            />
                        </div>

                        {{-- City, State, Postal Code --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">
                                    City *
                                </label>
                                <input 
                                    type="text" 
                                    id="shipping_city"
                                    wire:model.defer="shipping_city"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="New York"
                                />
                                @error('shipping_city')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">
                                    State *
                                </label>
                                <input 
                                    type="text" 
                                    id="shipping_state"
                                    wire:model.defer="shipping_state"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="NY"
                                />
                                @error('shipping_state')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Postal Code *
                                </label>
                                <input 
                                    type="text" 
                                    id="shipping_postal_code"
                                    wire:model.defer="shipping_postal_code"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                    placeholder="10001"
                                />
                                @error('shipping_postal_code')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Country --}}
                        <div>
                            <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country *
                            </label>
                            <select 
                                id="shipping_country"
                                wire:model.defer="shipping_country"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                            >
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="MX">Mexico</option>
                            </select>
                            @error('shipping_country')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-between pt-6">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Continue Shopping
                            </a>
                            
                            <button 
                                type="submit"
                                class="inline-flex items-center px-6 py-2 bg-pokemon-red hover:bg-red-600 text-white font-medium rounded-lg transition"
                            >
                                Continue to Payment
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- STEP 2: Payment Method --}}
            @if($this->step === 'payment')
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>
                    
                    <form wire:submit.prevent="nextStep" class="space-y-6">
                        {{-- Payment Method Selection --}}
                        <div class="space-y-4">
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        wire:model.defer="payment_method" 
                                        value="cod"
                                        class="h-4 w-4 text-pokemon-red focus:ring-pokemon-red border-gray-300"
                                        checked
                                    />
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Pay when your order arrives at your doorstep</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Future Payment Methods (Disabled) --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-100 opacity-60">
                                <label class="flex items-center cursor-not-allowed">
                                    <input 
                                        type="radio" 
                                        disabled
                                        class="h-4 w-4 text-gray-400 border-gray-300"
                                    />
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-500">Credit/Debit Card</span>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                                Coming Soon
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Visa, Mastercard, American Express</p>
                                    </div>
                                </label>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-100 opacity-60">
                                <label class="flex items-center cursor-not-allowed">
                                    <input 
                                        type="radio" 
                                        disabled
                                        class="h-4 w-4 text-gray-400 border-gray-300"
                                    />
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-500">PayPal</span>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                                Coming Soon
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Pay with your PayPal account</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Special Instructions --}}
                        <div>
                            <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-1">
                                Special Instructions (Optional)
                            </label>
                            <textarea 
                                id="special_instructions"
                                wire:model.defer="special_instructions"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pokemon-red focus:border-transparent"
                                placeholder="Any special delivery instructions or notes..."
                            ></textarea>
                            @error('special_instructions')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-between pt-6">
                            <button 
                                type="button"
                                wire:click="previousStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Shipping
                            </button>
                            
                            <button 
                                type="submit"
                                class="inline-flex items-center px-6 py-2 bg-pokemon-red hover:bg-red-600 text-white font-medium rounded-lg transition"
                            >
                                Review Order
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- STEP 3: Order Review --}}
            @if($this->step === 'review')
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Review</h2>
                    
                    <div class="space-y-8">
                        {{-- Order Summary --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @foreach($cartItems as $item)
                                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $item['product']['name'] ?? 'Product Name' }}</h4>
                                                <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] ?? 1 }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">
                                            ${{ number_format(($item['product']['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Shipping Information --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Address</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 font-medium">{{ $shipping_first_name }} {{ $shipping_last_name }}</p>
                                <p class="text-sm text-gray-600">{{ $shipping_address_line_1 }}</p>
                                @if($shipping_address_line_2)
                                    <p class="text-sm text-gray-600">{{ $shipping_address_line_2 }}</p>
                                @endif
                                <p class="text-sm text-gray-600">{{ $shipping_city }}, {{ $shipping_state }} {{ $shipping_postal_code }}</p>
                                <p class="text-sm text-gray-600">{{ $shipping_country }}</p>
                                <p class="text-sm text-gray-600 mt-2">{{ $shipping_email }}</p>
                                <p class="text-sm text-gray-600">{{ $shipping_phone }}</p>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                                </div>
                                @if($special_instructions)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-sm text-gray-600"><strong>Special Instructions:</strong></p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $special_instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Order Totals --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Total</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="text-gray-900">${{ number_format($shippingCost, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax ({{ $taxRate * 100 }}%)</span>
                                    <span class="text-gray-900">${{ number_format($taxAmount, 2) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-pokemon-red">${{ number_format($finalTotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-between pt-6">
                            <button 
                                type="button"
                                wire:click="previousStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Payment
                            </button>
                            
                            <button 
                                wire:click="placeOrder"
                                class="inline-flex items-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition transform hover:scale-105 active:scale-95"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Place Order
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Cart Summary Sidebar (Fixed) --}}
        <div class="mt-4 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cart Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ $cartCount }} item{{ $cartCount !== 1 ? 's' : '' }}</span>
                    <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                </div>
                @if($this->step === 'review')
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">${{ number_format($shippingCost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="text-gray-900">${{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between font-semibold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-pokemon-red">${{ number_format($finalTotal, 2) }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-500 mt-2">Shipping and tax calculated at checkout</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Include toast notification script if not already included --}}
@if(!isset($toastScriptIncluded))
<script>
    if (typeof window.showToast === 'undefined') {
        window.showToast = function(message, type = 'info') {
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        };
    }
</script>
@endif
