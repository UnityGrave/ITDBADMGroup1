<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect("/", navigate: true);
    }
};
?>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-6">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-pokemon-red rounded"></div>
                    <span class="text-lg font-display font-bold text-pokemon-black">{{ config('app.name') }}</span>
                </a>

                <div class="hidden sm:flex items-center space-x-6">
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center text-brand-gray-700 hover:text-pokemon-red transition">
                            Products
                            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            class="absolute z-50 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="{{ route('products.index') }}"
                                    class="block px-4 py-2 text-sm text-brand-gray-700 hover:bg-brand-gray-50" wire:navigate>
                                    All Products
                                </a>
                                <a href="{{ route('products.index', ['category' => 'Box']) }}"
                                    class="block px-4 py-2 text-sm text-brand-gray-700 hover:bg-brand-gray-50" wire:navigate>
                                    Accessory
                                </a>
                                <a href="{{ route('products.index', ['category' => 'Box']) }}"
                                    class="block px-4 py-2 text-sm text-brand-gray-700 hover:bg-brand-gray-50" wire:navigate>
                                    Box
                                </a>
                                <a href="{{ route('products.index', ['category' => 'Booster Pack']) }}"
                                    class="block px-4 py-2 text-sm text-brand-gray-700 hover:bg-brand-gray-50" wire:navigate>
                                    Booster Packs
                                </a>
                                <a href="{{ route('products.index', ['category' => 'Single Card']) }}"
                                    class="block px-4 py-2 text-sm text-brand-gray-700 hover:bg-brand-gray-50" wire:navigate>
                                    Single Cards
                                </a>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                        <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2 text-base font-medium bg-pokemon-red hover:bg-pokemon-red/90 text-white rounded-lg shadow transition w-full text-center mt-2">
                        <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Admin Dashboard
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->hasRole('Customer'))
                        <a href="#"
                            class="inline-flex items-center bg-pokemon-red hover:bg-red-600 text-white font-medium text-sm px-4 py-2 rounded-lg shadow transition group">
                            <!-- Trainer's Bag Icon -->
                            <svg class="h-5 w-5 mr-2 transition-transform group-hover:scale-110" viewBox="0 0 24 24" fill="none">
                                <!-- Bag Rectangle -->
                                <rect x="4" y="6" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <!-- Handle -->
                                <path d="M9 6C9 4.34315 10.3431 3 12 3C13.6569 3 15 4.34315 15 6" stroke="currentColor" stroke-width="1.5"/>
                                <!-- Poké Ball -->
                                <circle cx="12" cy="13" r="3" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M9 13h6" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="12" cy="13" r="1" fill="currentColor"/>
                            </svg>
                            Cart
                        </a>
                    @endif

                    <div class="hidden sm:flex items-center space-x-2">
                        <div class="text-right">
                            <div class="text-sm font-medium text-pokemon-black">{{ auth()->user()->name }}</div>
                            @if(auth()->user()->roles->count() > 0)
                                <div class="text-xs text-brand-gray-500">
                                    {{ auth()->user()->roles->first()->name }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-brand-gray-500 bg-white hover:text-brand-gray-700 focus:outline-none transition">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>Profile</x-dropdown-link>
                            <x-dropdown-link :href="route('orders.index')" wire:navigate>Orders</x-dropdown-link>
                            <x-dropdown-link :href="route('test.defense-in-depth')" wire:navigate>Security Tests</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="text-brand-gray-700 hover:text-pokemon-red transition">Login</a>
                        <a href="{{ route('register') }}" class="text-brand-gray-700 hover:text-pokemon-red transition">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-pokemon-black">{{ auth()->user()->name }}</div>
                <div class="font-medium text-sm text-brand-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>My Account</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.index')" wire:navigate>Orders</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('test.defense-in-depth')" wire:navigate>Security Tests</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
