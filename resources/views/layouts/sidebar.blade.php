<aside class="bg-indigo-900 text-white transition-all duration-300 flex-shrink-0 flex flex-col" :class="sidebarOpen ? 'w-64' : 'w-20'">
    <div class="p-6 flex items-center gap-3">
        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-900 font-bold">SC</div>
        <span class="font-bold text-xl tracking-tight" x-show="sidebarOpen">SciCouncil</span>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-2">
        <!-- Postulant Links -->
        <template x-if="role === 'postulant'">
            <div class="space-y-1">
                <a href="/postulant/dashboard" @click.prevent="navigateTo('postulant/dashboard')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/postulant/dashboard' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-chart-line w-6"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="/postulant/requests" @click.prevent="navigateTo('postulant/requests')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/postulant/requests' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-file-invoice w-6"></i>
                    <span x-show="sidebarOpen">Mes Demandes</span>
                </a>
                <a href="/postulant/profile" @click.prevent="navigateTo('postulant/profile')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/postulant/profile' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-circle-user w-6"></i>
                    <span x-show="sidebarOpen">Mon Profil</span>
                </a>
            </div>
        </template>


        <!-- Examiner Links -->
        <template x-if="role === 'examiner'">
            <div class="space-y-1">
                <a href="/examiner/dashboard" @click.prevent="navigateTo('examiner/dashboard')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/examiner/dashboard' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-gauge-high w-6"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="/examiner/notifications" @click.prevent="navigateTo('examiner/notifications')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/examiner/notifications' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-bell w-6"></i>
                    <span x-show="sidebarOpen">Notifications</span>
                </a>
            </div>
        </template>

        <!-- Sub-Commission Links -->
        <template x-if="role === 'president-sub'">
            <div class="space-y-1">
                <a href="/subcommission/dashboard" @click.prevent="navigateTo('subcommission/dashboard')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/subcommission/dashboard' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-gauge-high w-6"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="/subcommission/requests" @click.prevent="navigateTo('subcommission/requests')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/subcommission/requests' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-inbox w-6"></i>
                    <span x-show="sidebarOpen">Demandes Reçues</span>
                </a>
                <a href="/subcommission/reviews" @click.prevent="navigateTo('subcommission/reviews')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/subcommission/reviews' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-comments w-6"></i>
                    <span x-show="sidebarOpen">Suivi des Avis</span>
                </a>
                <a href="/subcommission/transmit" @click.prevent="navigateTo('subcommission/transmit')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/subcommission/transmit' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-paper-plane w-6"></i>
                    <span x-show="sidebarOpen">Transmettre</span>
                </a>
            </div>
        </template>

        <!-- Council President Links -->
        <template x-if="role === 'president-council'">
            <div class="space-y-1">
                <a href="/president/dashboard" @click.prevent="navigateTo('president/dashboard')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/president/dashboard' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-gauge-high w-6"></i>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="/president/dossiers" @click.prevent="navigateTo('president/dossiers')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/president/dossiers' || window.location.pathname.startsWith('/president/dossier/') ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-folder-open w-6"></i>
                    <span x-show="sidebarOpen">Dossiers Reçus</span>
                </a>
                <a href="/president/commissions" @click.prevent="navigateTo('president/commissions')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/president/commissions' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-sitemap w-6"></i>
                    <span x-show="sidebarOpen">Sous-Commissions</span>
                </a>
            </div>
        </template>

        <!-- Admin Links -->
        <template x-if="role === 'admin'">
            <div class="space-y-1">
                <a href="/admin/users" @click.prevent="navigateTo('admin/users')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/admin/users' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-users-gear w-6"></i>
                    <span x-show="sidebarOpen">Utilisateurs</span>
                </a>
                <a href="/admin/commissions" @click.prevent="navigateTo('admin/commissions')"
                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors"
                   :class="window.location.pathname === '/admin/commissions' ? 'bg-white/10' : ''">
                    <i class="fa-solid fa-sitemap w-6"></i>
                    <span x-show="sidebarOpen">Commissions</span>
                </a>
            </div>
        </template>
    </nav>

    <!-- Role Switcher (For Demo Consistency) -->
    <div class="p-4 border-t border-white/10">
        <label class="text-xs text-white/50 uppercase font-bold" x-show="sidebarOpen">Switch Role (Demo)</label>
        <select x-model="role" class="w-full mt-2 bg-indigo-800 text-white text-sm border-0 rounded p-1 focus:ring-0">
            <option value="postulant">Postulant</option>
            <option value="examiner">Examiner</option>
            <option value="president-sub">Sub-Comm Pres.</option>
            <option value="president-council">Council Pres.</option>
            <option value="admin">Admin</option>
        </select>
    </div>

    <div class="p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-red-500/20 text-red-300 transition-colors text-left">
                <i class="fa-solid fa-right-from-bracket w-6 shrink-0"></i>
                <span x-show="sidebarOpen" class="font-semibold text-sm">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
