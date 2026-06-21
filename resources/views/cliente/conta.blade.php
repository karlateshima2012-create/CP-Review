@extends('layouts.cliente')

@section('title', 'Configurações de Conta - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Conta</h2>
    <p class="text-body-m text-neutral-secondary">Dados do estabelecimento</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-16 py-12 mb-24 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-16 mb-24 rounded-lg shadow-sm" role="alert">
        <p class="font-bold">Ocorreu um erro:</p>
        <ul class="list-disc list-inside text-body-m mt-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('cliente.conta.update', $cliente->id) }}" method="POST">
    @csrf

    <div class="grid lg:grid-cols-12 gap-32 items-start">
        <!-- Left Side: Forms -->
        <div class="lg:col-span-7 space-y-24">
            
            <!-- Card 1: Estabelecimento -->
            <div class="card p-24">
                <div class="flex items-center gap-8 mb-24">
                    <div class="w-24 h-24 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h18v18H3V3z"></path></svg>
                    </div>
                    <h3 class="text-body-g font-bold text-neutral-primary">Estabelecimento</h3>
                </div>

                <div class="space-y-16">
                    <div>
                        <label class="block text-body-m font-bold text-neutral-secondary mb-4">Nome</label>
                        <input type="text" name="nome_empresa" value="{{ old('nome_empresa', $cliente->nome_empresa) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-body-m font-bold text-neutral-secondary mb-4">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-body-m font-bold text-neutral-secondary mb-4">Link do Google Maps</label>
                        <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $cliente->google_maps_link) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="https://maps.google.com/...">
                        <p class="text-legend text-neutral-secondary/60 mt-4">Usado para redirecionar clientes satisfeitos.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-16">
                        <div>
                            <label class="block text-body-m font-bold text-neutral-secondary mb-4">Telefone WhatsApp</label>
                            <input type="text" name="telefone_whatsapp" value="{{ old('telefone_whatsapp', $cliente->telefone_whatsapp) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Ex: 5511999999999">
                        </div>
                        <div>
                            <label class="block text-body-m font-bold text-neutral-secondary mb-4">LINE User ID (Japão)</label>
                            <input type="text" name="line_user_id" value="{{ old('line_user_id', $cliente->line_user_id) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Alterar Senha -->
            <div class="card p-24">
                <div class="flex items-center gap-8 mb-24">
                    <div class="w-24 h-24 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"></path></svg>
                    </div>
                    <h3 class="text-body-g font-bold text-neutral-primary">Alterar senha</h3>
                </div>

                <div class="space-y-16">
                    <div>
                        <label class="block text-body-m font-bold text-neutral-secondary mb-4">Senha atual</label>
                        <input type="password" name="current_password" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="••••••••">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-16">
                        <div>
                            <label class="block text-body-m font-bold text-neutral-secondary mb-4">Nova senha</label>
                            <input type="password" name="password" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Mínimo 8 caracteres">
                        </div>
                        <div>
                            <label class="block text-body-m font-bold text-neutral-secondary mb-4">Confirmar</label>
                            <input type="password" name="password_confirmation" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Repetir senha">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto bg-brand-600 text-white hover:bg-brand-700 px-24 py-12 rounded-lg text-body-m font-bold transition flex items-center justify-center gap-8 shadow-sm">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                Salvar alterações
            </button>
        </div>

        <!-- Right Side: Plan & Support -->
        <div class="lg:col-span-5 space-y-24">
            
            <!-- Plan Details -->
            <div class="bg-brand-50 border border-brand-200 rounded-lg p-24 space-y-16">
                <span class="text-legend text-brand-600 font-bold uppercase tracking-wider">Seu Plano</span>
                
                <div>
                    <h3 class="text-title-2 font-bold text-brand-900 leading-tight">CP Review</h3>
                    <div class="flex items-baseline gap-4 mt-4 text-brand-800">
                        <span class="text-title-1 font-bold">¥ 4.800</span>
                        <span class="text-body-m">/ mês</span>
                    </div>
                </div>

                <ul class="space-y-8 pt-8 border-t border-brand-200">
                    <li class="flex items-center gap-8 text-body-m text-brand-800 font-medium">
                        <svg class="w-16 h-16 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                        <span>Avaliações ilimitadas</span>
                    </li>
                    <li class="flex items-center gap-8 text-body-m text-brand-800 font-medium">
                        <svg class="w-16 h-16 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                        <span>Bot em 2 idiomas</span>
                    </li>
                    <li class="flex items-center gap-8 text-body-m text-brand-800 font-medium">
                        <svg class="w-16 h-16 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                        <span>Painel de ocorrências</span>
                    </li>
                    <li class="flex items-center gap-8 text-body-m text-brand-800 font-medium">
                        <svg class="w-16 h-16 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                        <span>QR Code personalizado</span>
                    </li>
                    <li class="flex items-center gap-8 text-body-m text-brand-800 font-medium">
                        <svg class="w-16 h-16 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                        <span>Suporte via WhatsApp</span>
                    </li>
                </ul>

                <p class="text-legend text-brand-600/70 font-semibold pt-4">
                    Renova em {{ $cliente->trial_ends_at ? $cliente->trial_ends_at->format('d/m/Y') : now()->addMonth()->format('01/m/Y') }}
                </p>
            </div>

            <!-- Support Links -->
            <div class="card p-24 space-y-16">
                <span class="text-legend text-neutral-secondary font-bold uppercase tracking-wider">Suporte</span>
                
                <div class="space-y-12">
                    <a href="https://wa.me/819011886491" target="_blank" class="w-full border border-neutral-border hover:bg-neutral-bg py-12 px-16 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center gap-8 transition">
                        <span>💬 Falar com suporte (WhatsApp)</span>
                    </a>
                    <a href="{{ route('cliente.ajuda', $cliente->id) }}" class="w-full border border-neutral-border hover:bg-neutral-bg py-12 px-16 rounded-lg text-body-m font-bold text-neutral-primary flex items-center justify-center gap-8 transition">
                        <span>📄 Guia de uso</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
