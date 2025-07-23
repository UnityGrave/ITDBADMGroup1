<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('products.index', absolute: false), navigate: true);
    }
}; ?>

<div class="max-h-screen bg-gradient-to-br from-blue-100 via-white to-blue-100 py-12 flex items-center justify-center">
    <div class="max-w-md w-full">
        <!-- Pokeball Icon -->
        <div class="flex justify-center mb-4">
            <svg class="w-16 h-16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="22" fill="#fff" stroke="#222" stroke-width="2"/>
                <path d="M2 24h44" stroke="#222" stroke-width="2"/>
                <circle cx="24" cy="24" r="8" fill="#fff" stroke="#222" stroke-width="2"/>
                <circle cx="24" cy="24" r="4" fill="#facc15" stroke="#222" stroke-width="2"/>
            </svg>
        </div>
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 drop-shadow">Welcome Back</h1>
            <p class="text-blue-700 mt-2 font-medium">Sign in to your Konibui account</p>
        </div>
        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-blue-200">
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-green-800 text-sm">{{ session('status') }}</p>
                </div>
            @endif
            <form wire:submit="login" class="space-y-5">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        wire:model="form.email" 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Enter your email address"
                    />
                    @error('form.email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        wire:model="form.password" 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Enter your password"
                    />
                    @error('form.password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        wire:model="form.remember" 
                        id="remember" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-400"
                    />
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>
                <!-- Submit Button -->
                <div class="pt-2">
                    <x-primary-button class="w-full bg-blue-400 hover:bg-blue-500 text-gray-900 text-base font-bold rounded-xl">
                        <span class="mr-2">🔑</span> Sign In
                    </x-primary-button>
                </div>
            </form>
            <!-- Action Links -->
            <div class="mt-6 text-center space-y-2">
                <p class="text-gray-600">
                    <a href="{{ route('password.request') }}" class="text-gray-600 hover:text-blue-800 font-medium">
                        Forgot your password?
                    </a>
                </p>
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Create one here
                    </a>
                </p>
            </div>
        </div>
        <!-- Back to Home -->
        <div class="mt-4 text-center">
            <a href="/" class="text-gray-400 hover:text-gray-700 text-sm">
                ← Back to Home
            </a>
        </div>
    </div>
</div>
