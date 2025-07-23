<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-brand-gray-300 rounded-lg font-display text-sm text-brand-gray-700 tracking-wide hover:text-brand-gray-900 focus:outline-none focus:ring-2 focus:ring-pokemon-red focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
