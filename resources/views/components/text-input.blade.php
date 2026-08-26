@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#2b909a] focus:ring-[#2b909a] rounded-md shadow-sm']) }}>
