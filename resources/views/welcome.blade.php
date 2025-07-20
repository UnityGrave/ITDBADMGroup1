<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konibui - Testing Interface</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-6 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Konibui E-commerce Platform</h1>
            <p class="text-gray-600">Authentication Testing Interface</p>
                                </div>

        <!-- Authentication Status -->
        <div class="max-w-md mx-auto mb-8">
            @auth
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-green-800 font-semibold">✅ Authenticated</div>
                    <div class="text-green-600 text-sm mt-1">Welcome, {{ Auth::user()->name }}!</div>
                    <div class="mt-3">
                        <a href="{{ route('dashboard') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mr-2">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</button>
                        </form>
                                        </div>
                                    </div>
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-blue-800 font-semibold">🔒 Not Authenticated</div>
                    <div class="text-blue-600 text-sm mt-1">Please login or register to continue</div>
                                </div>
            @endauth
                                </div>

        <!-- Authentication Links -->
        <div class="max-w-sm mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Authentication Testing</h2>
                
                <div class="space-y-3">
                    @guest
                        <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            🔑 Login
                        </a>
                        
                        <a href="{{ route('register') }}" class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                            📝 Register
                        </a>
                        
                        <a href="{{ route('password.request') }}" class="block w-full bg-gray-600 text-white text-center py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                            🔄 Reset Password
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            🏠 Dashboard
                        </a>
                        
                        <a href="{{ route('profile') }}" class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            👤 Profile
                        </a>
                    @endguest
                                </div>
                            </div>
                        </div>

        <!-- Testing Information -->
        <div class="max-w-2xl mx-auto mt-12">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🧪 Testing Information</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Environment Status</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>✅ Laravel {{ app()->version() }}</li>
                            <li>✅ Database: MySQL ({{ config('database.connections.mysql.database') }})</li>
                            <li>✅ Mail: {{ config('mail.default') }} driver</li>
                            <li>✅ Session: {{ config('session.driver') }} driver</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Available Routes</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• /login - User login</li>
                            <li>• /register - User registration</li>
                            <li>• /forgot-password - Password reset</li>
                            <li>• /dashboard - User dashboard</li>
                            <li>• /profile - User profile</li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </body>
</html>
