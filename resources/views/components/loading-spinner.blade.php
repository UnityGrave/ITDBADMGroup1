@props(['size' => 'md'])

@php
$sizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    'lg' => 'w-8 h-8',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<style>
.pokeball-spin {
    animation: pokeball-spin 3s linear infinite;
}

@keyframes pokeball-spin {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(180deg); }
    50% { transform: rotate(360deg); }
    75% { transform: rotate(540deg); }
    100% { transform: rotate(720deg); }
}
</style>

<div {{ $attributes->merge(['class' => 'relative ' . $sizeClass]) }}>
    <div class="pokeball-spin">
        <!-- Poké Ball -->
        <svg viewBox="0 0 100 100" class="w-full h-full">
            <!-- Top half (red) -->
            <path d="M50 10 A40 40 0 0 1 90 50 H10 A40 40 0 0 1 50 10Z" fill="#EE1515"/>
            <!-- Bottom half (white) -->
            <path d="M50 90 A40 40 0 0 1 10 50 H90 A40 40 0 0 1 50 90Z" fill="white"/>
            <!-- Center circle (white) -->
            <circle cx="50" cy="50" r="12" fill="white" stroke="#222224" stroke-width="4"/>
            <!-- Center dot (black) -->
            <circle cx="50" cy="50" r="6" fill="#222224"/>
            <!-- Outer ring -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#222224" stroke-width="4"/>
            <!-- Middle line -->
            <line x1="10" y1="50" x2="90" y2="50" stroke="#222224" stroke-width="4"/>
        </svg>
    </div>
</div> 