@props(['title' => null, 'subtitle' => null, 'footer' => null, 'noPadding' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300']) }}>
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($headerAction))
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 italic text-sm text-slate-500">
            {{ $footer }}
        </div>
    @endif
</div>
