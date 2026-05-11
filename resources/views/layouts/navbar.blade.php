<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10 shrink-0">
    <div class="flex items-center gap-4">
        <button @click="toggleSidebar()" class="text-slate-500 hover:text-indigo-600 p-2 rounded-lg hover:bg-slate-50 transition-colors">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
        <div class="relative group hidden md:block">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" placeholder="Rechercher..." class="pl-10 pr-4 py-2 bg-slate-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 w-64 lg:w-96 transition-all">
        </div>
    </div>

    <div class="flex items-center gap-4">
        {{-- Notification bell --}}
        <button class="relative p-2 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-50 transition-colors">
            <i class="fa-regular fa-bell text-xl"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        </button>

        <div class="h-8 w-px bg-slate-200 mx-2"></div>

        {{-- User info --}}
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-slate-900">
                    {{ Auth::user()->prenom ?? '' }} {{ Auth::user()->nom ?? 'Utilisateur' }}
                </p>
                <p class="text-xs text-slate-500 uppercase tracking-wider">
                    {{ Auth::user()->role ?? '' }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border-2 border-transparent hover:border-indigo-400 transition-all overflow-hidden">
                <img src="https://ui-avatars.com/api/?name={{ urlencode((Auth::user()->prenom ?? 'U') . '+' . (Auth::user()->nom ?? '')) }}&background=6366f1&color=fff"
                     alt="Avatar">
            </div>
        </div>


    </div>
</header>
