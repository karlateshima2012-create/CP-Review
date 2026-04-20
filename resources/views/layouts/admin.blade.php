<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CP Review Care - Admin @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex flex-col h-screen overflow-hidden">
    @if(session()->has('impersonate_tenant_id'))
    <div class="bg-red-600 text-white py-2 px-4 flex justify-between items-center text-[10px] font-bold uppercase tracking-widest z-[9999] shadow-lg flex-shrink-0">
        <div class="flex items-center gap-2">
            <span>🕵️ SUPORTE ATIVO</span>
        </div>
        <a href="{{ route('admin.stop-impersonation') }}" class="bg-white text-red-600 px-3 py-1 rounded-lg text-[9px]">SAIR</a>
    </div>
    @endif

    <!-- Mobile Header -->
    <header class="lg:hidden bg-purple-900 text-white p-4 flex justify-between items-center z-40 shadow-md">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-purple-700 rounded-lg flex items-center justify-center font-bold">CP</div>
            <span class="font-black tracking-tighter">REVIEW</span>
        </div>
        <button id="toggle-sidebar" class="p-2 rounded-xl bg-purple-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </header>
    
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-purple-950 text-white transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex-shrink-0 flex flex-col shadow-2xl">
            <div class="p-8 flex justify-between items-center lg:block">
                <div>
                    <h1 class="text-2xl font-black tracking-tighter">CP REVIEW <span class="text-purple-400">CARE</span></h1>
                    <p class="text-[10px] text-purple-400 uppercase tracking-widest font-bold mt-1 opacity-60">SaaS Administration</p>
                </div>
                <button id="close-sidebar" class="lg:hidden p-2 text-purple-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="mt-4 px-6 space-y-2 flex-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/20' : 'text-purple-300/80 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-bold text-sm">Dashboard Master</span>
                </a>
                <a href="{{ route('admin.clientes') }}" class="flex items-center px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.clientes') ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/20' : 'text-purple-300/80 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-bold text-sm">Gestão de Tenants</span>
                </a>
                <a href="{{ route('admin.transacoes') }}" class="flex items-center px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.transacoes') ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/20' : 'text-purple-300/80 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="font-bold text-sm">Faturamento / Planos</span>
                </a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.notifications') ? 'bg-white/10 text-white shadow-inner ring-1 ring-white/20' : 'text-purple-300/80 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="font-bold text-sm">Monitor de Envio</span>
                </a>
            </nav>

            <div class="p-8 border-t border-white/5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Efetuar Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-purple-950/80 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 flex flex-col">
            <div class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-30 hidden lg:block">
                <div class="px-10 py-6 flex justify-between items-center">
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">@yield('header')</h2>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sistema Operacional</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 p-4 lg:p-0">
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
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            document.body.classList.remove('overflow-hidden');
        }

        if(toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    </script>
</body>
</body>
</html>
