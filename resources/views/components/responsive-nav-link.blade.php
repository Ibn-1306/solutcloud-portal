@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 text-start text-base font-black text-teal-700 bg-teal-50 border-l-4 border-teal-500 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 text-start text-base font-bold text-gray-600 hover:text-teal-600 hover:bg-teal-50 hover:border-teal-200 border-l-4 border-transparent transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>