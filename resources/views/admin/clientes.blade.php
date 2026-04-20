@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Export -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Gestão de Tenants</h1>
            <p class="text-sm text-gray-500">Gerencie todos os lojistas ativos na plataforma</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.clientes.export') }}" class="bg-white text-gray-700 px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 hover:bg-gray-50 transition flex items-center gap-2">
                📥 Exportar CSV
            </a>
            <button onclick="alert('Funcionalidade em desenvolvimento: Novos clientes devem ser aprovados via Transações para garantir o fluxo financeiro.')" class="bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                + Novo Cliente
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.clientes') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Buscar por empresa, e-mail ou slug..." class="w-full rounded-xl border-gray-200 text-sm focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <select name="plano" class="w-full rounded-xl border-gray-200 text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Todos os Planos</option>
                    <option value="standard" {{ request('plano') == 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="premium" {{ request('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="elite" {{ request('plano') == 'elite' ? 'selected' : '' }}>Elite</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-xl border-gray-200 text-sm focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Qualquer Status</option>
                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition">
                Filtrar Resultados
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Empresa / Slug</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Contato de Notificação</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Plano / Billing</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Ações Master</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clientes as $cliente)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-[10px] text-gray-300 font-mono">...{{ substr($cliente->id, -8) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-100 w-10 h-10 rounded-lg flex items-center justify-center text-lg">🏢</div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $cliente->nome_empresa }}</div>
                                    <div class="text-[10px] bg-gray-100 inline-block px-1.5 py-0.5 rounded text-gray-500 font-mono">/avaliar/{{ $cliente->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 space-y-0.5">
                                <div class="flex items-center gap-2"><span>📧</span> {{ $cliente->email }}</div>
                                @if($cliente->telefone_whatsapp)
                                <div class="flex items-center gap-2"><span>📱</span> {{ $cliente->telefone_whatsapp }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="capitalize px-2 py-0.5 rounded-full text-[10px] font-bold text-center w-20
                                    {{ $cliente->plano == 'premium' ? 'bg-purple-100 text-purple-700' : ($cliente->plano == 'standard' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $cliente->plano }}
                                </span>
                                <div class="text-[10px] text-gray-400 font-medium">Status: <span class="text-green-600">{{ $cliente->status }}</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <!-- Impersonate -->
                                <a href="{{ route('admin.clientes.impersonate', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-orange-100 hover:text-orange-700 rounded-lg transition" title="Logar como este Tenant">
                                    🕵️
                                </a>
                                <!-- Ver Avaliações (Suporte) -->
                                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" target="_blank" class="p-2 bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg transition" title="Ver Avaliações">
                                    📋
                                </a>
                                <!-- QR -->
                                <a href="{{ route('admin.clientes.qrcode', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg transition" title="Gerar QR Code">
                                    🖼️
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition" title="Editar">
                                    ✏️
                                </a>
                                <!-- Delete -->
                                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isso apagará TODOS os dados deste lojista. Confirmar?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2 bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-lg transition">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">
                            Nenhum tenant encontrado com os filtros selecionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $clientes->links() }}
        </div>
    </div>
</div>
@endsection
