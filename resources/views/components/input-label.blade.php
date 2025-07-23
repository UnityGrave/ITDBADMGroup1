@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-display font-medium text-pokemon-black']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500 ml-1">*</span>
    @endif
</label>
