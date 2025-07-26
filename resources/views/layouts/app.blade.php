<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased h-full bg-brand-white text-brand-gray-900">
    <div class="min-h-screen">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {!! $slot !!}
        </main>
    </div>

    <!-- Loading Indicator -->
    <div 
        wire:loading.class.remove="opacity-0 scale-90"
        class="fixed bottom-4 right-4 bg-pokemon-black text-white px-4 py-2 rounded-full shadow-lg transform transition-all duration-300 opacity-0 scale-90 flex items-center gap-2"
    >
        <div class="animate-spin">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <span class="text-sm font-medium">Loading...</span>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // Enhanced toast notification system
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const colors = {
                success: 'bg-green-600 text-white border-green-700',
                error: 'bg-red-600 text-white border-red-700',
                info: 'bg-blue-600 text-white border-blue-700',
                warning: 'bg-yellow-500 text-gray-900 border-yellow-600'
            };
            
            const icons = {
                success: '✅',
                error: '❌',
                info: 'ℹ️',
                warning: '⚠️'
            };
            
            toast.className = `${colors[type]} px-4 py-3 rounded-lg shadow-xl border-2 transform transition-all duration-300 translate-x-full opacity-0 max-w-sm min-w-64`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-lg flex-shrink-0">${icons[type]}</span>
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);
            
            // Animate out and remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (container.contains(toast)) {
                        container.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        };
    </script>
</body>
</html>
