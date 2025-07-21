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

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Welcome Back</h1>
            <p class="text-gray-600 mt-2">Sign in to your Konibui account</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-green-800 text-sm">{{ session('status') }}</p>
                </div>
            @endif

            <form wire:submit="login" class="space-y-4">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your email address"
                    />
                    @error('form.email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        wire:model="form.password" 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    />
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 focus:outline-none transition-colors"
                    >
                        🔑 Sign In
                    </button>
                </div>
            </form>

            <!-- Action Links -->
            <div class="mt-6 text-center space-y-2">
                <p class="text-gray-600">
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Forgot your password?
                    </a>
                </p>
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800 font-medium">
                        Create one here
                    </a>
                </p>
            </div>
        </div>

        <!-- Testing Helper -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">🧪 Testing Helper</h4>
            <div class="text-xs text-blue-600 space-y-1">
                <p>• Use any valid email format for testing</p>
                <p>• Passwords are validated and hashed securely</p>
                <p>• Sessions are regenerated on successful login</p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-4 text-center">
            <a href="/" class="text-gray-500 hover:text-gray-700 text-sm">
                ← Back to Home
            </a>
        </div>
    </div>
</div>
