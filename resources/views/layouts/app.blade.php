<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scientific Council Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .transition-main { transition: all 0.3s ease-in-out; }
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; }
        @keyframes loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" x-data="app()">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            @include('layouts.navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 transition-main" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

    <script>
        function app() {
            return {
                sidebarOpen: true,
                role: 'postulant',
                page: 'dashboard',
                init() {
                    // Auto-detect role from current URL
                    const path = window.location.pathname;
                    if (path.startsWith('/subcommission')) {
                        this.role = 'president-sub';
                    } else if (path.startsWith('/examiner')) {
                        this.role = 'examiner';
                    } else if (path.startsWith('/president')) {
                        this.role = 'president-council';
                    } else if (path.startsWith('/admin')) {
                        this.role = 'admin';
                    } else {
                        this.role = 'postulant';
                    }
                    console.log('App initialized — role:', this.role);
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                loadInitialPage() {
                    // Logic to load dashboard on start
                }
            }
        }

        // Page Navigation — real redirect with brief skeleton flash
        function navigateTo(url) {
            const main = document.getElementById('main-content');

            // Show skeleton for visual feedback, then navigate immediately
            main.style.opacity = '0.4';
            main.innerHTML = `
                <div class="space-y-4 animate-pulse">
                    <div class="h-8 w-1/3 skeleton rounded-lg"></div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="h-28 skeleton rounded-xl"></div>
                        <div class="h-28 skeleton rounded-xl"></div>
                        <div class="h-28 skeleton rounded-xl"></div>
                        <div class="h-28 skeleton rounded-xl"></div>
                    </div>
                    <div class="h-10 w-1/4 skeleton rounded-lg"></div>
                    <div class="h-64 skeleton rounded-xl"></div>
                </div>
            `;

            // Navigate immediately — no artificial delay
            window.location.href = '/' + url;
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            toast.className = `${bg} text-white px-6 py-3 rounded-lg shadow-xl transform transition-all duration-300 translate-y-10 opacity-0 flex items-center justify-between min-w-[300px]`;
            toast.innerHTML = `<span><i class="fa-solid fa-${type === 'success' ? 'check' : 'xmark'} mr-2"></i> ${message}</span>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
    @stack('scripts')
</body>
</html>
