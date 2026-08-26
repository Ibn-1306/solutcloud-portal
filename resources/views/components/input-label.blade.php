@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#1A2E35] tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>