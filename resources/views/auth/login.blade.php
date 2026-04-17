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

        <div class="mt-6 pt-6 border-t border-white/10 text-center">
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
