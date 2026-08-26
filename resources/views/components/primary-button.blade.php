<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center px-6 py-3 bg-[#2b909a] border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-[#237781] focus:bg-[#237781] active:bg-[#1d6269] focus:outline-none focus:ring-2 focus:ring-[#2b909a] focus:ring-offset-2 transition ease-in-out duration-200 shadow-lg shadow-cyan-100'
]) }}>
    {{ $slot }}
</button>