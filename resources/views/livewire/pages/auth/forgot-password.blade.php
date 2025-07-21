<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-gray-600 mt-2">We'll send you a password reset link</p>
        </div>

        <!-- Reset Password Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Success Status -->
            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="text-green-800 text-sm">
                            ✅ {{ session('status') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-4 text-sm text-gray-600">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </div>

            <form wire:submit="sendPasswordResetLink" class="space-y-4">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        wire:model="email" 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        autofocus
                        autocomplete="username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your email address"
                    />
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 focus:ring-4 focus:ring-gray-200 focus:outline-none transition-colors"
                    >
                        🔄 Email Password Reset Link
                    </button>
                </div>
            </form>

            <!-- Action Links -->
            <div class="mt-6 text-center space-y-2">
                <p class="text-gray-600">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Sign in here
                    </a>
                </p>
                <p class="text-gray-600">
                    Need an account? 
                    <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800 font-medium">
                        Create one here
                    </a>
                </p>
            </div>
        </div>

        <!-- Testing Helper -->
        <div class="mt-6 bg-yellow-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-yellow-800 mb-2">🧪 Testing Helper</h4>
            <div class="text-xs text-yellow-700 space-y-1">
                <p>• Password reset emails are logged to storage/logs/ for testing</p>
                <p>• Use any registered email address to receive a reset link</p>
                <p>• Check Laravel logs for the reset URL in development</p>
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
