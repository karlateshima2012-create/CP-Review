@extends('layouts.admin')

@section('title', 'Onboarding de Novo Cliente')

@section('header', 'Onboarding: Novo Tenant')

@section('content')
<div class="p-4 lg:p-10 max-w-4xl mx-auto">
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-purple-900/5 border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-700 to-indigo-800 p-8 lg:p-12 text-white">
            <h3 class="text-3xl font-black tracking-tighter">Iniciar Onboarding</h3>
            <p class="text-purple-200 mt-2 text-sm lg:text-base font-medium opacity-80">Preencha os dados do contrato para gerar o ambiente automaticamente.</p>
        </div>

        <form action="{{ route('admin.clientes.store') }}" method="POST" class="p-8 lg:p-12 space-y-10">
            @csrf
            
            <!-- Seção Dados da Empresa -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-black text-xs">01</span>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Identidade & Acesso</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">Nome da Empresa</label>
                        <input type="text" name="nome_empresa" required placeholder="Ex: Starbucks Shinjuku" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">E-mail Administrativo (Login)</label>
                        <input type="email" name="email" required placeholder="contato@empresa.com" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">Região / País</label>
                        <select name="pais" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900 appearance-none">
                            <option value="jp">Japão (JP)</option>
                            <option value="br" selected>Brasil (BR)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">Canal de Notificação</label>
                        <select name="canal_notificacao" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900 appearance-none">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="line">LINE</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">WhatsApp (Envio)</label>
                        <input type="text" name="telefone_whatsapp" placeholder="81 90-..." class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900">
                    </div>
                </div>
            </div>

            <!-- Seção Configuração de Plano -->
            <div class="space-y-6 pt-10 border-t border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-black text-xs">02</span>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Plano & Google Maps</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">Plano SaaS</label>
                        <select name="plano" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900 appearance-none">
                            <option value="standard">Standard (Básico)</option>
                            <option value="pro">Pro (Com Logo no QR)</option>
                            <option value="elite" selected>Elite (Full Branding)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-700 uppercase ml-1">Valor Mensal (Ex: 15000.00)</label>
                        <input type="number" step="0.01" name="valor_mensal" value="0" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 uppercase ml-1">Link Google Maps (Review)</label>
                    <input type="url" name="google_maps_link" placeholder="https://g.page/r/..." class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all font-bold text-gray-900">
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-purple-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3">
                    🚀 Finalizar Configuração e Enviar Credenciais
                </button>
                <p class="text-center text-[10px] text-gray-400 mt-6 font-bold uppercase tracking-widest">O sistema irá gerar o banco de dados, script do bot e QR Code automaticamente.</p>
            </div>
        </form>
    </div>
</div>
@endsection
