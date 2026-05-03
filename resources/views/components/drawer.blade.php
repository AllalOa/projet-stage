@props(['id', 'title' => 'Details'])

<div 
    x-cloak
    x-show="$store.drawers && $store.drawers.open === '{{ $id }}'"
    class="fixed inset-0 z-50 overflow-hidden"
    role="dialog"
    aria-modal="true"
>
    <!-- Overlay -->
    <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm transition-opacity" 
         x-show="$store.drawers.open === '{{ $id }}'"
         x-transition:enter="ease-in-out duration-500"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.drawers.close()"></div>

    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        <div class="w-screen max-w-2xl"
             x-show="$store.drawers.open === '{{ $id }}'"
             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
             
            <div class="h-full flex flex-col bg-white shadow-2xl">
                <div class="px-6 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800">{{ $title }}</h2>
                    <button @click="$store.drawers.close()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </button>
                </div>
                
                <div class="relative flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </div>

                @if(isset($footer))
                    <div class="shrink-0 px-6 py-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('drawers', {
            open: null,
            openDrawer(id) { this.open = id; },
            close() { this.open = null; }
        })
    })
</script>
