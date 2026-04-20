@extends('layouts.app')

@section('title', 'Acesso ao Sistema')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F9FAFB] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl border border-gray-100 shadow-xl">
        <div>
            <div class="mx-auto flex items-center justify-center">
                <img src="/logo.png" alt="CP Review" class="h-16 w-auto">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 tracking-tight">
                Bem-vindo de volta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-500">
                Acesse seu painel administrativo ou de lojista
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input id="email" name="email" type="email" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-200 bg-gray-50 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent sm:text-sm transition-all" placeholder="exemplo@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-200 bg-gray-50 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent sm:text-sm transition-all" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600">
                        Lembrar-me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="https://wa.me/819011886491" target="_blank" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
                        Esqueceu a senha?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all transform hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-purple-200">
                    Acessar Painel
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-gray-50 pt-8">
            <p class="text-xs text-gray-400">
                Acesso exclusivo para administradores e estabelecimentos parceiros.
            </p>
            <a href="{{ url('/') }}" class="mt-4 inline-block text-xs font-medium text-gray-500 hover:text-purple-600 underline decoration-gray-200">
                ← Voltar para a Home
            </a>
        </div>
    </div>
</div>
@endsection
