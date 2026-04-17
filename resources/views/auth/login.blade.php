@extends('layouts.app')

@section('title', 'Acesso ao Sistema')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-700 via-purple-600 to-blue-500 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white/10 backdrop-blur-xl p-10 rounded-3xl border border-white/20 shadow-2xl">
        <div>
            <div class="mx-auto h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-lg transform -rotate-6">
                <span class="text-3xl">⭐</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Bem-vindo de volta
            </h2>
            <p class="mt-2 text-center text-sm text-white/70">
                Acesse seu painel administrativo ou de lojista
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="email" class="sr-only">E-mail</label>
                    <input id="email" name="email" type="email" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-white/10 bg-white/20 placeholder-white/50 text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent sm:text-sm transition-all" placeholder="Seu e-mail">
                    @error('email')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Senha</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-white/10 bg-white/20 placeholder-white/50 text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent sm:text-sm transition-all" placeholder="Sua senha">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-white/20 rounded bg-white/20">
                    <label for="remember_me" class="ml-2 block text-sm text-white/80">
                        Lembrar-me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="https://wa.me/5511999999999" target="_blank" class="font-medium text-white hover:text-purple-200 transition-colors">
                        Esqueceu a senha?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    Acessar Painel
                </button>
            </div>
        </form>

        <!-- 🛠️ ACESSOS DE TESTE (Modo de Desenvolvimento) -->
        <div class="mt-8 pt-8 border-t border-white/20">
            <h3 class="text-white text-xs font-bold uppercase tracking-widest text-center mb-4 opacity-60">Consoles de Teste</h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ url('/avaliar/sabor-senior') }}" target="_blank" class="flex items-center justify-center gap-2 p-3 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-white text-sm transition-all group">
                    <span class="text-lg group-hover:scale-125 transition-transform">📱</span>
                    <span>Simular Visão: <b>Ver Bot (Cliente)</b></span>
                </a>
                
                <div class="p-4 bg-purple-900/30 rounded-2xl border border-white/5 space-y-2">
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-white/60">🔑 Login Admin:</span>
                        <code class="text-purple-200">admin@cpreview.com / admin123</code>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-white/60">🔑 Login Lojista:</span>
                        <code class="text-purple-300">loja@teste.com / loja123</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-white/50 italic">
                Acesso exclusivo para administradores e estabelecimentos parceiros.
            </p>
            <a href="{{ url('/') }}" class="mt-4 inline-block text-xs text-white/70 hover:text-white underline decoration-white/30">
                ← Voltar para a Home
            </a>
        </div>
    </div>
</div>
@endsection
