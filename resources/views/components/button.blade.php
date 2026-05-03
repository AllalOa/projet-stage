@props(['type' => 'primary', 'size' => 'md', 'icon' => null])

@php
    $bases = 'inline-flex items-center justify-center font-semibold transition-all duration-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $types = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-lg shadow-indigo-200',
        'secondary' => 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 focus:ring-slate-200',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-400 shadow-lg shadow-red-200',
        'success' => 'bg-emerald-500 text-white hover:bg-emerald-600 focus:ring-emerald-400 shadow-lg shadow-emerald-200',
        'ghost' => 'text-slate-500 hover:bg-slate-100 focus:ring-slate-200',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
    ];
@endphp

<button {{ $attributes->merge(['class' => "$bases {$types[$type]} {$sizes[$size]}"]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-2' }}"></i>
    @endif
    {{ $slot }}
</button>
