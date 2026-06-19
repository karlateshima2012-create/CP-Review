@extends('layouts.admin')

@section('title', 'Novo Cliente')

@section('header', 'Onboarding: Novo Tenant')

@section('content')
<div class="p-4 lg:p-10 max-w-4xl mx-auto">
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-purple-900/5 border border-gray-100 overflow-hidden">

        <div class="bg-gradient-to-r from-purple-700 to-indigo-800 p-8 lg:p-12 text-white">
            <h3 class="text-3xl font-black tracking-tighter">Iniciar Onboarding</h3>
            <p class="text-purple-200 mt-2 text-sm lg:text-base font-medium opacity-80">
                Preencha os dados do contrato para gerar o ambiente automaticamente.
            </p>
        </div>

        @if ($errors->any())
        <div class="mx-8 mt-8 bg-red-50 border border-red-200 text-red-700 p-5 rounded-2xl text-sm">
            <p class="font-bold mb-2">Por favor corrija os erros abaixo:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.clientes.store') }}" method="POST" class="p-8 lg:p-12 space-y-10">
            @csrf

            {{-- Campos compartilhados (pack, nome, email, canal, maps) --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-black text-xs">01</span>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Idioma, Identidade & Acesso</h4>
                </div>
                @include('partials._cliente-fields', ['defaults' => []])
            </div>

            {{-- Campos exclusivos do admin --}}
            <div class="space-y-6 pt-10 border-t border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-black text-xs">02</span>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Plano & Faturamento</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Plano SaaS</label>
                        <select name="plano" class="w-full px-5 py-4 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900 appearance-none text-sm">
                            <option value="standard">Standard</option>
                            <option value="pro">Pro (Com Logo no QR)</option>
                            <option value="elite" selected>Elite (Full Branding)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Valor Mensal (¥)</label>
                        <input type="number" step="0.01" name="valor_mensal" value="4800"
                               class="w-full px-5 py-4 rounded-2xl border border-gray-200 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900 text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-purple-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3">
                    🚀 Finalizar Configuração e Enviar Credenciais
                </button>
                <p class="text-center text-[10px] text-gray-400 mt-6 font-bold uppercase tracking-widest">
                    O sistema irá gerar o bot nos 2 idiomas do pacote selecionado e enviar as credenciais por e-mail.
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
