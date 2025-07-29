<div x-data="{ open: false }" class="relative">
    <!-- Currency Switcher Button -->
    <button 
        @click="open = !open" 
        @click.away="open = false"
        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-brand-gray-700 hover:text-pokemon-red transition-colors duration-200 rounded-lg hover:bg-brand-gray-50"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <!-- Current Currency Icon/Symbol -->
        <span class="text-base font-bold">{{ $this->getCurrentSymbol() }}</span>
        
        <!-- Current Currency Code -->
        <span class="hidden sm:inline">{{ $selectedCurrency }}</span>
        
        <!-- Dropdown Arrow -->
        <svg 
            class="w-4 h-4 transform transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-56 origin-top-right bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-2">
            <!-- Header -->
            <div class="px-4 py-2 text-xs font-semibold text-brand-gray-500 uppercase tracking-wider border-b border-brand-gray-100">
                Select Currency
            </div>
            
            <!-- Currency Options -->
            @foreach($currencies as $currency)
                <button
                    wire:click="changeCurrency('{{ $currency->code }}')"
                    @click="open = false"
                    class="w-full px-4 py-3 text-left hover:bg-brand-gray-50 transition-colors duration-150 flex items-center justify-between group
                        {{ $selectedCurrency === $currency->code ? 'bg-pokemon-red/5 text-pokemon-red' : 'text-brand-gray-900' }}"
                >
                    <div class="flex items-center gap-3">
                        <!-- Currency Symbol -->
                        <span class="w-6 text-center font-bold text-base">
                            {{ $currency->symbol }}
                        </span>
                        
                        <!-- Currency Details -->
                        <div>
                            <div class="font-medium">{{ $currency->code }}</div>
                            <div class="text-xs text-brand-gray-500">{{ $currency->name }}</div>
                        </div>
                    </div>
                    
                    <!-- Selected Indicator -->
                    @if($selectedCurrency === $currency->code)
                        <svg class="w-4 h-4 text-pokemon-red" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </button>
            @endforeach
            
            <!-- Footer Info -->
            @auth
                <div class="px-4 py-2 mt-2 text-xs text-brand-gray-500 border-t border-brand-gray-100">
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Preference saved to your account
                    </div>
                </div>
            @else
                <div class="px-4 py-2 mt-2 text-xs text-brand-gray-500 border-t border-brand-gray-100">
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Saved for this session only
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="changeCurrency" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded-lg">
        <svg class="animate-spin h-4 w-4 text-pokemon-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
