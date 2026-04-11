@props(['active', 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-black text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-300 group'
            : 'flex items-center px-4 py-3 text-sm font-bold text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-xl transition-all duration-300 group';
@endphp

<a @click="if(window.innerWidth < 1024) sidebarOpen = false" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <div class="mr-4 flex-shrink-0 {{ ($active ?? false) ? 'text-white' : 'text-gray-400 group-hover:text-indigo-500' }} transition-colors">
            {!! $icon !!}
        </div>
    @endif
    <span class="uppercase tracking-tighter">{{ $slot }}</span>
</a>
