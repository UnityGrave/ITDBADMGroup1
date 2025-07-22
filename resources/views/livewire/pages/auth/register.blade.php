<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';


    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {


        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        // Assign default 'Customer' role to new user
        $user->assignRole('Customer');

        Auth::login($user);

        $this->redirect(route('products.index', absolute: false), navigate: true);
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
            <h1 class="text-3xl font-extrabold text-gray-800 drop-shadow">Create Account</h1>
            <p class="text-blue-700 mt-2 font-medium">Join Konibui E-commerce Platform</p>
        </div>
        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-blue-200">
            <form wire:submit.prevent="register" class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                        Full Name
                    </label>
                    <input 
                        wire:model="name" 
                        id="name" 
                        name="name" 
                        type="text" 
                        required 
                        autofocus 
                        autocomplete="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Enter your full name"
                    />
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        wire:model="email" 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        autocomplete="username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Enter your email address"
                    />
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        wire:model="password" 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Create a password"
                    />
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <input 
                        wire:model="password_confirmation" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-blue-50"
                        placeholder="Confirm your password"
                    />
                    @error('password_confirmation')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Submit Button -->
                <div class="pt-2">
                    <x-primary-button class="w-full bg-blue-400 hover:bg-blue-500 text-gray-900 text-base font-bold rounded-xl">
                        <span class="mr-2">📝</span> Create Account
                    </x-primary-button>
                </div>
            </form>
            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Sign in here
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
