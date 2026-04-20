<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CP Review Care - Admin @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'IBM Plex Sans', sans-serif; }
        .font-display { font-family: 'Bebas Neue', cursive; }
        .font-mono { font-family: 'IBM Plex Mono', monospace; }
        .sidebar-item-active { background-color: #F5F3FF; color: #7C3AED; }
    </style>
</head>
<body class="bg-[#F9FAFB] h-screen overflow-hidden text-[#111827]">
    
    <!-- Mobile Header -->
    <header class="lg:hidden bg-white border-b border-gray-100 p-4 flex justify-between items-center z-40">
        <div class="flex items-center gap-2">
            <span class="font-display text-2xl text-[#7C3AED] tracking-tight">CP REVIEW</span>
        </div>
        <button id="toggle-sidebar" class="p-2 rounded-xl bg-gray-50 text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </header>
    
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#F8FAFC] border-r border-[#E5E7EB] transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex-shrink-0 flex flex-col">
            <div class="p-10 flex justify-between items-center lg:block">
                <div>
                    <h1 class="font-display text-3xl text-[#7C3AED] tracking-wider">CP REVIEW <span class="text-gray-400">CARE</span></h1>
                    <p class="text-[9px] text-gray-400 uppercase tracking-[0.3em] font-bold mt-1">System Authority</p>
                </div>
                <button id="close-sidebar" class="lg:hidden p-2 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="mt-4 px-6 space-y-1 flex-1 overflow-y-auto">
                <p class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">Principal</p>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active shadow-sm ring-1 ring-purple-100' : 'text-gray-500 hover:bg-gray-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="font-bold text-sm">Dashboard Master</span>
                </a>

                <a href="{{ route('admin.clientes') }}" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.clientes*') ? 'sidebar-item-active shadow-sm ring-1 ring-purple-100' : 'text-gray-500 hover:bg-gray-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-bold text-sm">Gestão de Tenants</span>
                </a>

                <p class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-widest pt-6 mb-4">Operações</p>

                <a href="{{ route('admin.transacoes') }}" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.transacoes') ? 'sidebar-item-active shadow-sm ring-1 ring-purple-100' : 'text-gray-500 hover:bg-gray-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="font-bold text-sm">Faturamento / Planos</span>
                </a>
                
                <a href="{{ route('admin.notifications') }}" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.notifications') ? 'sidebar-item-active shadow-sm ring-1 ring-purple-100' : 'text-gray-500 hover:bg-gray-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-bold text-sm">Monitor de Envio</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'sidebar-item-active shadow-sm ring-1 ring-purple-100' : 'text-gray-500 hover:bg-gray-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m32-2v2a4 4 0 01-4 4h-2a4 4 0 01-4-4v-2m-9 1a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                    <span class="font-bold text-sm">Central de Relatórios</span>
                </a>
            </nav>

            <div class="px-8 py-10 space-y-6 border-t border-gray-100">
                @if(session()->has('impersonate_tenant_id'))
                <a href="{{ route('admin.stop-impersonation') }}" class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-100 transition-all">
                    <span>🕵️ Parar Suporte</span>
                </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-400 hover:text-[#7C3AED] transition-colors text-sm font-bold group">
                        <svg class="w-5 h-5 mr-3 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>

                <div class="p-5 bg-purple-50 rounded-3xl">
                    <p class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-2">Suporte</p>
                    <a href="#" class="text-xs font-bold text-purple-600 hover:underline">Central de Ajuda</a>
                </div>
            </div>
        </aside>

        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header Desktop -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30 hidden lg:block">
                <div class="px-10 py-6 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">SaaS Management</p>
                        <h2 class="text-2xl font-black text-[#111827] tracking-tighter">@yield('header')</h2>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <input type="text" placeholder="Global search..." class="bg-gray-50 border-none rounded-full px-5 py-2 text-xs w-64 focus:ring-2 focus:ring-purple-500/20">
                        </div>
                        <div class="flex items-center gap-3 pl-6 border-l border-gray-100">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ativo</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-0">
                @if(session('success'))
                <div class="m-8 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }

        if(toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    </script>
</body>
</html>
