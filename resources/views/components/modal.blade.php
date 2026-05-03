@props(['id', 'title' => 'Modal Title', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-6xl',
        'full' => 'max-w-full m-4',
    ];
@endphp

<div 
    x-cloak
    x-show="$store.modals && $store.modals.open === '{{ $id }}'"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto"
    role="dialog"
    aria-modal="true"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
>
    <!-- Overlay -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$store.modals.close()"></div>

    <!-- Modal Content -->
    <div class="relative bg-white rounded-3xl shadow-2xl w-full {{ $sizes[$size] }} flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-900">{{ $title }}</h3>
            <button @click="$store.modals.close()" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="p-8 overflow-y-auto">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('modals', {
            open: null,
            openModal(id) { this.open = id; },
            close() { this.open = null; }
        })
    })
</script>
