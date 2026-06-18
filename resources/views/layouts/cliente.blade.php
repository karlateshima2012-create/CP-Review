@extends('layouts.app')

@section('title', $title ?? 'Painel Lojista')

@section('content')
<div class="min-h-screen bg-neutral-bg flex flex-col md:flex-row">
    
    <!-- Desktop Sidebar (Hidden on Mobile) -->
    <aside class="hidden md:flex flex-col w-64 bg-neutral-card border-r border-neutral-border h-screen fixed top-0 left-0 z-40 p-24 justify-between">
        <div class="space-y-32">
            <!-- Brand & Company Info -->
            <div class="flex items-center gap-12">
                <div class="w-32 h-32 bg-brand-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 .587l3.668 7.431 8.2 1.191-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.132 9.209l8.2-1.191L12 .587z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <span class="text-body-g font-bold text-neutral-primary block">CP Review</span>
                    <span class="text-[11px] text-neutral-secondary block truncate max-w-[140px]">{{ $cliente->nome_empresa }}</span>
                </div>
            </div>

            <!-- Vertical Navigation Menu -->
            <nav class="space-y-8">
                <!-- Dashboard -->
                <a href="{{ route('cliente.dashboard', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.dashboard') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Ocorrências -->
                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.avaliacoes') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Ocorrências</span>
                </a>

                <!-- QR Code -->
                <a href="{{ route('cliente.qrcode-link', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.qrcode-link') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h.008v.008H15V15Zm0 2.25h.008v.008H15v-.008ZM17.25 15h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008ZM15 19.5h.008v.008H15V19.5Zm2.25 0h.008v.008h-.008V19.5ZM19.5 15h.008v.008H19.5V15Zm0 2.25h.008v.008H19.5v-.008ZM19.5 19.5h.008v.008H19.5V19.5Z"></path>
                    </svg>
                    <span>QR Code</span>
                </a>

                <!-- Bot -->
                <a href="{{ route('cliente.bot', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.bot') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"></path>
                    </svg>
                    <span>Bot de Avaliação</span>
                </a>

                <!-- Conta -->
                <a href="{{ route('cliente.conta', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.conta') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path>
                    </svg>
                    <span>Minha Conta</span>
                </a>
            </nav>
        </div>

        <!-- Logout Bottom -->
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full border border-neutral-border hover:bg-neutral-bg py-12 rounded-lg text-body-m font-bold text-neutral-secondary transition flex items-center justify-center gap-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"></path></svg>
                Sair da Conta
            </button>
        </form>
    </aside>

    <!-- Main Workspace (Offsets left sidebar on desktop) -->
    <div class="flex-1 flex flex-col md:pl-64 min-h-screen">
        
        <!-- Mobile Header (Hidden on Desktop) -->
        <header class="md:hidden bg-neutral-card border-b border-neutral-border sticky top-0 z-40">
            <div class="px-16 py-12 flex justify-between items-center">
                <!-- Brand -->
                <div class="flex items-center gap-8">
                    <div class="w-24 h-24 bg-brand-600 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.431 8.2 1.191-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.132 9.209l8.2-1.191L12 .587z"/>
                        </svg>
                    </div>
                    <span class="text-body-m font-bold text-neutral-primary">CP Review</span>
                    <span class="text-neutral-secondary">|</span>
                    <span class="text-[11px] text-neutral-secondary truncate max-w-[120px]">{{ $cliente->nome_empresa }}</span>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="border border-neutral-border hover:bg-neutral-bg px-12 py-6 rounded-lg text-body-m font-medium text-neutral-secondary transition">
                        Sair
                    </button>
                </form>
            </div>
        </header>

        <!-- Main View Content -->
        <main class="flex-1 px-16 py-24 md:p-32 pb-[100px] md:pb-32">
            @yield('cliente_content')
        </main>
    </div>

    <!-- Mobile Navigation Tab Bar (Hidden on Desktop) -->
    <nav class="md:hidden bg-neutral-card border-t border-neutral-border fixed bottom-0 left-0 right-0 z-50">
        <div class="max-w-md mx-auto px-16 py-8 flex justify-between items-center">
            <!-- Tab 1: Dashboard -->
            <a href="{{ route('cliente.dashboard', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.dashboard') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Tab 2: Ocorrências -->
            <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.avaliacoes') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>Ocorr.</span>
            </a>

            <!-- Tab 3: QR Code -->
            <a href="{{ route('cliente.qrcode-link', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.qrcode-link') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h.008v.008H15V15Zm0 2.25h.008v.008H15v-.008ZM17.25 15h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008ZM15 19.5h.008v.008H15V19.5Zm2.25 0h.008v.008h-.008V19.5ZM19.5 15h.008v.008H19.5V15Zm0 2.25h.008v.008H19.5v-.008ZM19.5 19.5h.008v.008H19.5V19.5Z"></path>
                </svg>
                <span>QR Code</span>
            </a>

            <!-- Tab 4: Bot -->
            <a href="{{ route('cliente.bot', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.bot') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"></path>
                </svg>
                <span>Bot</span>
            </a>

            <!-- Tab 5: Conta -->
            <a href="{{ route('cliente.conta', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.conta') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path>
                </svg>
                <span>Conta</span>
            </a>
        </div>
    </nav>
</div>
@endsection
