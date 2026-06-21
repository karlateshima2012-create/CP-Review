@extends('layouts.app')

@section('title', $title ?? 'Painel Lojista')

@section('content')
<div class="min-h-screen bg-neutral-bg flex flex-col md:flex-row">
    
    <!-- Desktop Sidebar (Hidden on Mobile) -->
    <aside class="hidden md:flex flex-col w-[260px] bg-neutral-card border-r border-neutral-border h-screen fixed top-0 left-0 z-40 p-24 justify-between">
        <div class="space-y-32">
            <!-- Brand & Company Info -->
            <div class="flex items-center gap-12">
                <img src="/favicon.svg?v={{ file_exists(public_path('favicon.svg')) ? filemtime(public_path('favicon.svg')) : time() }}" alt="CP Review" class="w-32 h-32 flex-shrink-0">
                <svg viewBox="0 0 120 32" class="h-32 w-[120px] flex-shrink-0" style="font-family: 'IBM Plex Sans', sans-serif;">
                    <text x="0" y="13" font-size="13.5" font-weight="700" fill="#111827" textLength="120" lengthAdjust="spacing">CP REVIEW</text>
                    <text x="0" y="29" font-size="7.2" font-weight="700" fill="#4B5563" textLength="120" lengthAdjust="spacing">GESTÃO DE AVALIAÇÕES</text>
                </svg>
            </div>

            <!-- Vertical Navigation Menu -->
            <nav class="space-y-8">
                <!-- Dashboard -->
                <a href="{{ route('cliente.dashboard', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.dashboard') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-24 h-24 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Ocorrências -->
                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.avaliacoes') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-24 h-24 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Ocorrências</span>
                </a>

                <!-- QR Code -->
                <a href="{{ route('cliente.qrcode-link', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.qrcode-link') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-24 h-24 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h.008v.008H15V15Zm0 2.25h.008v.008H15v-.008ZM17.25 15h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008ZM15 19.5h.008v.008H15V19.5Zm2.25 0h.008v.008h-.008V19.5ZM19.5 15h.008v.008H19.5V19.5Zm0 2.25h.008v.008H19.5v-.008ZM19.5 19.5h.008v.008H19.5V19.5Z"></path>
                    </svg>
                    <span>Divulgação</span>
                </a>

                <!-- Personalização -->
                <a href="{{ route('cliente.bot', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.bot') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-24 h-24 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span>Personalização</span>
                </a>

                <!-- Conta -->
                <a href="{{ route('cliente.conta', $cliente->id) }}" class="flex items-center gap-12 px-12 py-8 rounded-lg text-body-m font-bold transition {{ Route::is('cliente.conta') ? 'bg-brand-50 text-brand-600' : 'text-neutral-secondary hover:bg-neutral-bg hover:text-brand-600' }}">
                    <svg class="w-24 h-24 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path>
                    </svg>
                    <span>Minha Conta</span>
                </a>
            </nav>
        </div>

        <!-- Language Toggle -->
        <div class="flex gap-8 mb-12">
            <a href="{{ route('set-locale', 'pt') }}" class="flex-1 text-center py-6 rounded-lg text-legend font-bold border transition {{ app()->getLocale() === 'pt' ? 'bg-brand-50 text-brand-600 border-brand-200' : 'border-neutral-border text-neutral-secondary hover:bg-neutral-bg' }}">🇧🇷 PT</a>
            <a href="{{ route('set-locale', 'ja') }}" class="flex-1 text-center py-6 rounded-lg text-legend font-bold border transition {{ app()->getLocale() === 'ja' ? 'bg-brand-50 text-brand-600 border-brand-200' : 'border-neutral-border text-neutral-secondary hover:bg-neutral-bg' }}">🇯🇵 JA</a>
        </div>

        <!-- Logout Bottom -->
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full border border-neutral-border hover:bg-neutral-bg py-12 rounded-lg text-body-m font-bold text-neutral-secondary transition flex items-center justify-center gap-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"></path></svg>
                {{ __('Sair da Conta') }}
            </button>
        </form>
    </aside>

    <!-- Main Workspace (Offsets left sidebar on desktop) -->
    <div class="flex-1 flex flex-col md:pl-[260px] min-h-screen">
        
        <!-- Mobile Header (Hidden on Desktop) -->
        <header class="md:hidden bg-neutral-card border-b border-neutral-border sticky top-0 z-40">
            <div class="px-16 py-12 flex justify-between items-center">
                <!-- Brand -->
                <div class="flex items-center gap-8">
                    <img src="/favicon.svg?v={{ file_exists(public_path('favicon.svg')) ? filemtime(public_path('favicon.svg')) : time() }}" alt="CP Review" class="w-24 h-24 flex-shrink-0">
                    <span class="text-body-m font-bold uppercase tracking-wider text-neutral-primary">CP REVIEW</span>
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
                <span>Divulgar</span>
            </a>

            <!-- Tab 4: Personalização -->
            <a href="{{ route('cliente.bot', $cliente->id) }}" class="flex flex-col items-center gap-4 text-[10px] font-medium transition {{ Route::is('cliente.bot') ? 'text-brand-600' : 'text-neutral-secondary hover:text-brand-600' }}">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                <span>Pers.</span>
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
@unless(session('impersonate_tenant_id'))
{{-- ONBOARDING TOUR --}}
<div id="ob" style="display:none;position:fixed;inset:0;z-index:110;align-items:center;justify-content:center;padding:16px;" aria-modal="true" role="dialog" aria-label="Tour de boas-vindas CP Review">
  <div style="position:absolute;inset:0;background:rgba(8,8,20,0.82);backdrop-filter:blur(8px);"></div>
  <div id="ob-card" style="position:relative;background:white;border-radius:24px;box-shadow:0 32px 80px rgba(0,0,0,0.45);width:100%;max-width:640px;overflow:hidden;">

    <button onclick="obDone()" id="ob-skip" style="position:absolute;top:13px;right:13px;z-index:20;border:none;background:rgba(0,0,0,0.18);color:rgba(255,255,255,0.75);font-size:11px;font-weight:600;padding:5px 12px 5px 10px;border-radius:20px;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px);transition:background .15s;">
      Pular
      <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </button>

    <div id="ob-track" style="position:relative;overflow:hidden;"></div>

    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 24px;border-top:1px solid #F3F4F6;">
      <div id="ob-dots" style="display:flex;gap:5px;align-items:center;"></div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button id="ob-prev-btn" onclick="obNav(-1)" style="display:none;border:none;background:none;color:#9CA3AF;font-size:12px;font-weight:600;padding:8px 12px;border-radius:8px;cursor:pointer;font-family:inherit;transition:color .15s;">← Voltar</button>
        <button id="ob-next-btn" onclick="obNav(1)" style="background:#7C3AED;color:white;border:none;font-size:13px;font-weight:700;padding:10px 22px;border-radius:10px;cursor:pointer;font-family:inherit;transition:background .15s;">Próximo →</button>
      </div>
    </div>
  </div>
</div>

<style>
.ob-layout{display:flex;flex-direction:row;}
.ob-art{flex-shrink:0;width:220px;min-height:300px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
.ob-body{flex:1;padding:32px 28px;display:flex;flex-direction:column;justify-content:center;gap:13px;min-height:300px;}
@media(max-width:540px){
  .ob-layout{flex-direction:column;}
  .ob-art{width:100%;min-height:150px;}
  .ob-body{padding:20px;min-height:auto;gap:9px;}
}
@keyframes ob-fwd{from{opacity:0;transform:translateX(44px)}to{opacity:1;transform:translateX(0)}}
@keyframes ob-back{from{opacity:0;transform:translateX(-44px)}to{opacity:1;transform:translateX(0)}}
@keyframes ob-in{from{opacity:0;transform:scale(.97)}to{opacity:1;transform:scale(1)}}
.ob-anim-fwd{animation:ob-fwd .32s cubic-bezier(.25,.46,.45,.94) both}
.ob-anim-back{animation:ob-back .32s cubic-bezier(.25,.46,.45,.94) both}
.ob-anim-in{animation:ob-in .28s ease both}
#ob-next-btn:hover{background:#6D28D9!important}
#ob-prev-btn:hover{color:#374151!important}
</style>

<script>
(function(){
  var KEY='cp_ob_v1_{{ $cliente->id }}';
  if(localStorage.getItem(KEY))return;

  var nome=@json($cliente->nome_empresa);

  var STEPS=[
    {
      grad:'linear-gradient(140deg,#7C3AED 0%,#5B21B6 100%)',
      tag:'Bem-vindo',
      title:'Olá, '+nome+'!',
      desc:'Seu painel de reputação online está pronto. Em 4 passos você aprende tudo que precisa para colher resultados desde o primeiro dia.',
      icon:'<svg width="52" height="52" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
    },
    {
      grad:'linear-gradient(140deg,#2563EB 0%,#1E3A8A 100%)',
      tag:'Dashboard',
      title:'Acompanhe sua nota em tempo real',
      desc:'Veja a média de avaliações, distribuição por estrelas e o histórico de satisfação — tudo atualizado automaticamente a cada nova avaliação.',
      icon:'<svg width="52" height="52" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm6.75-8.25c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 019.75 20.625v-15zm6.75 4.5c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-10.5z"/></svg>',
    },
    {
      grad:'linear-gradient(140deg,#DC2626 0%,#7F1D1D 100%)',
      tag:'Ocorrências',
      title:'Proteja sua reputação antes do Google',
      desc:'Avaliações negativas ficam registradas aqui, não no Google. Você é notificado, resolve internamente e evita que a insatisfação vire resenha pública.',
      icon:'<svg width="52" height="52" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
    },
    {
      grad:'linear-gradient(140deg,#059669 0%,#064E3B 100%)',
      tag:'Divulgação',
      title:'Um scan, uma avaliação',
      desc:'Imprima o QR Code e cole nas mesas ou balcão. O cliente escaneia, avalia — e se satisfeito — vai direto ao seu perfil no Google.',
      icon:'<svg width="52" height="52" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zm0 9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zm9.75-9.75c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zm1.5 10.5h.75v.75h-.75v-.75zm0 2.25h.75v.75h-.75v-.75zm2.25-2.25h.75v.75h-.75v-.75zm0 2.25h.75v.75h-.75v-.75zm2.25-2.25h.75v.75h-.75v-.75zm0 2.25h.75v.75h-.75v-.75z"/></svg>',
    },
    {
      grad:'linear-gradient(140deg,#7C3AED 0%,#5B21B6 100%)',
      tag:'Tudo certo!',
      title:'Seu painel está pronto.',
      desc:'Configure o bot no seu idioma, imprima o QR Code e comece a coletar avaliações hoje. Qualquer dúvida, o suporte está disponível via WhatsApp.',
      icon:'<svg width="52" height="52" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
      cta:true,
    },
  ];

  var cur=0,total=STEPS.length;

  function slide(i,dir){
    var s=STEPS[i];
    var ac=dir===0?'ob-anim-in':(dir>0?'ob-anim-fwd':'ob-anim-back');
    return '<div class="ob-layout '+ac+'">'+
      '<div class="ob-art" style="background:'+s.grad+'">'+
        '<div style="position:absolute;width:190px;height:190px;border-radius:50%;background:rgba(255,255,255,0.07);bottom:-55px;right:-55px;"></div>'+
        '<div style="position:absolute;width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,0.09);top:-25px;left:-25px;"></div>'+
        '<div style="position:absolute;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.13);top:45%;right:16%;"></div>'+
        '<div style="position:relative;z-index:1;width:84px;height:84px;border-radius:22px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);">'+s.icon+'</div>'+
        '<div style="position:absolute;bottom:14px;left:50%;transform:translateX(-50%);font-size:10px;color:rgba(255,255,255,0.45);font-weight:600;letter-spacing:.1em;white-space:nowrap;">'+(i+1)+' / '+total+'</div>'+
      '</div>'+
      '<div class="ob-body">'+
        '<span style="font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#7C3AED;background:#F5F3FF;border:1px solid #DDD6FE;border-radius:20px;padding:3px 10px;display:inline-block;">'+s.tag+'</span>'+
        '<h2 style="font-size:20px;font-weight:800;color:#111827;line-height:1.25;margin:0;">'+s.title+'</h2>'+
        '<p style="font-size:13px;color:#6B7280;line-height:1.65;margin:0;">'+s.desc+'</p>'+
        (s.cta?'<button onclick="obDone()" style="margin-top:4px;background:#7C3AED;color:white;border:none;border-radius:10px;padding:11px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;width:fit-content;transition:background .15s;" onmouseover="this.style.background=\'#6D28D9\'" onmouseout="this.style.background=\'#7C3AED\'">Acessar o painel <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></button>':'')+
      '</div>'+
    '</div>';
  }

  function sync(dir){
    document.getElementById('ob-track').innerHTML=slide(cur,dir);

    document.getElementById('ob-dots').innerHTML=STEPS.map(function(_,i){
      return '<div style="height:6px;border-radius:99px;background:'+(i===cur?'#7C3AED':'#E5E7EB')+';width:'+(i===cur?'20px':'6px')+';transition:all .3s ease;"></div>';
    }).join('');

    var prev=document.getElementById('ob-prev-btn');
    var next=document.getElementById('ob-next-btn');
    prev.style.display=cur>0?'':'none';
    if(cur===total-1){
      next.textContent='Concluir';
      next.onclick=obDone;
    } else {
      next.innerHTML='Próximo →';
      next.onclick=function(){obNav(1);};
    }

    document.getElementById('ob-skip').style.display=cur===total-1?'none':'';
  }

  window.obNav=function(dir){
    var n=Math.max(0,Math.min(total-1,cur+dir));
    if(n===cur)return;
    cur=n;sync(dir);
  };

  window.obDone=function(){
    localStorage.setItem(KEY,'1');
    var el=document.getElementById('ob');
    el.style.transition='opacity .3s';
    el.style.opacity='0';
    setTimeout(function(){el.style.display='none';el.style.opacity='';},300);
  };

  var el=document.getElementById('ob');
  el.style.display='flex';
  sync(0);

  document.getElementById('ob').addEventListener('click',function(e){if(e.target===this)obDone();});

  document.addEventListener('keydown',function kh(e){
    if(!document.getElementById('ob')||document.getElementById('ob').style.display==='none'){document.removeEventListener('keydown',kh);return;}
    if(e.key==='ArrowRight'||e.key===' ')obNav(1);
    else if(e.key==='ArrowLeft')obNav(-1);
    else if(e.key==='Escape')obDone();
  });
})();
</script>
@endunless

@endsection
