@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Action -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Gestão de Clientes</h1>
            <p class="text-sm text-gray-500 font-medium">Gerencie todos os lojistas ativos na plataforma</p>
        </div>
        <div>
            <a href="{{ route('admin.clientes.create') }}" class="bg-[#7C3AED] text-white px-6 py-4 rounded-2xl text-base font-bold hover:bg-[#6D28D9] transition duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Novo Cliente
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-24 rounded-3xl border border-gray-100 shadow-sm mb-8">
        <form action="{{ route('admin.clientes') }}" method="GET" class="flex flex-col md:flex-row gap-16 items-center flex-wrap">
            <!-- Search Box -->
            <div class="relative w-full md:w-[320px]">
                <span class="absolute inset-y-0 left-0 pl-16 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por empresa, e-mail..." class="w-full pl-48 pr-12 py-12 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-700 placeholder-gray-400 focus:bg-white focus:border-purple-300 focus:ring focus:ring-purple-200/50 transition duration-200">
            </div>
            
            <!-- Select Plan -->
            <div class="w-full md:w-[180px]">
                <select name="plano" class="w-full py-12 px-16 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-600 focus:bg-white focus:border-purple-300 focus:ring focus:ring-purple-200/50 transition duration-200 cursor-pointer">
                    <option value="">Todos os Planos</option>
                    <option value="standard" {{ request('plano') == 'standard' ? 'selected' : '' }}>Standard</option>
                    <option value="premium" {{ request('plano') == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="elite" {{ request('plano') == 'elite' ? 'selected' : '' }}>Elite</option>
                </select>
            </div>

            <!-- Select Status -->
            <div class="w-full md:w-[160px]">
                <select name="status" class="w-full py-12 px-16 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-600 focus:bg-white focus:border-purple-300 focus:ring focus:ring-purple-200/50 transition duration-200 cursor-pointer">
                    <option value="">Qualquer Status</option>
                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full md:w-auto px-24 py-12 bg-gray-900 text-white rounded-2xl text-sm font-bold hover:bg-gray-800 transition duration-200 flex items-center justify-center gap-8 shadow-sm">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591l.04 3.061M3.75 6h16.5M3 10h18M3 14h18M3 18h18" />
                </svg>
                Filtrar
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
                                <!-- Editar -->
                                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition flex items-center justify-center text-gray-500" title="Editar">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </a>
                                <!-- Ver Avaliações (Suporte) -->
                                <a href="{{ route('cliente.avaliacoes', $cliente->id) }}" target="_blank" class="p-2 bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg transition flex items-center justify-center text-gray-500" title="Ver Avaliações">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-3.658C3.03 15.931 2 14.072 2 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                </a>
                                <!-- QR -->
                                <a href="{{ route('admin.clientes.qrcode', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg transition flex items-center justify-center text-gray-500" title="Gerar QR Code">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM3.75 14.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM14.625 3.75c-.621 0-1.125.504-1.125 1.125v2.25c0 .621.504 1.125 1.125 1.125h2.25c.621 0 1.125-.504 1.125-1.125v-2.25c0-.621-.504-1.125-1.125-1.125h-2.25zM16.5 16.5h.008v.008H16.5V16.5zm-3 0h.008v.008h-.008V16.5zm-3-3h.008v.008h-.008v-.008zm-3 0h.008v.008H7.5v-.008zm0-3h.008v.008H7.5V10.5zm3 0h.008v.008h-.008V10.5zm3 0h.008v.008h-.008V10.5zm3-3h.008v.008H16.5V7.5zm-3 0h.008v.008h-.008V7.5z" />
                                    </svg>
                                </a>
                                <!-- Impersonate -->
                                <a href="{{ route('admin.clientes.impersonate', $cliente->id) }}" class="p-2 bg-gray-100 hover:bg-orange-100 hover:text-orange-700 rounded-lg transition flex items-center justify-center text-gray-500" title="Logar como este Cliente">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </a>
                                <!-- Delete -->
                                <button
                                    type="button"
                                    onclick="openDeleteModal('{{ route('admin.clientes.destroy', $cliente->id) }}', '{{ addslashes($cliente->nome_empresa) }}')"
                                    class="p-2 bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-lg transition flex items-center justify-center text-gray-500"
                                    title="Excluir cliente">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">
                            Nenhum cliente encontrado com os filtros selecionados.
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
<!-- Modal de confirmação de exclusão -->
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-red-600 px-6 py-5 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-bold text-base">Excluir cliente permanentemente</h3>
                <p class="text-red-100 text-xs mt-0.5">Esta ação não pode ser desfeita</p>
            </div>
        </div>

        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 leading-relaxed">
                Você está prestes a excluir <span id="modal-nome" class="font-bold text-gray-900"></span> e todos os seus dados — avaliações, configurações e histórico serão apagados para sempre.
            </p>

            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <p class="text-xs font-bold text-red-700 mb-2">Para confirmar, digite <span class="font-mono bg-red-100 px-1.5 py-0.5 rounded">excluir</span> abaixo:</p>
                <input
                    id="delete-confirm-input"
                    type="text"
                    autocomplete="off"
                    placeholder="excluir"
                    oninput="onDeleteInput(this)"
                    class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-400 placeholder-red-300"
                >
            </div>

            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 border border-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button
                        id="delete-confirm-btn"
                        type="submit"
                        disabled
                        class="flex-1 bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition disabled:opacity-40 disabled:cursor-not-allowed hover:bg-red-700 enabled:hover:bg-red-700">
                        Excluir permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(action, nome) {
    document.getElementById('delete-form').action = action;
    document.getElementById('modal-nome').textContent = nome;
    document.getElementById('delete-confirm-input').value = '';
    document.getElementById('delete-confirm-btn').disabled = true;
    document.getElementById('delete-modal').classList.remove('hidden');
    document.getElementById('delete-confirm-input').focus();
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

function onDeleteInput(input) {
    document.getElementById('delete-confirm-btn').disabled = input.value !== 'excluir';
}

document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

@endsection
