@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 text-m font-black leading-5 text-teal-700 bg-teal-50 border border-teal-100 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-2 text-m font-bold leading-5 text-gray-500 hover:text-teal-600 hover:bg-teal-50 hover:border-teal-100 border border-transparent rounded-lg transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>