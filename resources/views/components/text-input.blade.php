@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-brand-gray-300 text-brand-gray-900 focus:border-pokemon-red focus:ring-pokemon-red rounded-lg shadow-sm']) !!}>
