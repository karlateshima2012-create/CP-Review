@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Clientes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalClientes }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Avaliações</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalAvaliacoes }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Média de Notas</p>
                    <p class="text-3xl font-bold text-yellow-500">{{ number_format($mediaNotas, 1) }}⭐</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Negativas Pendentes</p>
                    <p class="text-3xl font-bold text-red-500">{{ $negativasPendentes }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Transações Pendentes -->
    <div class="bg-white rounded-xl shadow-sm mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">🆕 Novos Cadastros Pendentes</h2>
            <p class="text-sm text-gray-500">{{ count($transacoesPendentes) }} aguardando aprovação</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">País</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transacoesPendentes as $transacao)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transacao->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $transacao->empresa }}</div>
                            <div class="text-sm text-gray-500">{{ $transacao->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2 py-1 rounded-full text-xs font-medium
                                {{ $transacao->plano == 'premium' ? 'bg-purple-100 text-purple-700' : ($transacao->plano == 'standard' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $transacao->plano }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">R$ {{ number_format($transacao->valor, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            📱 {{ $transacao->telefone }}<br>
                            @if($transacao->line_id)
                            💬 {{ $transacao->line_id }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm">{{ $transacao->pais == 'br' ? '🇧🇷 Brasil' : '🇯🇵 Japão' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button onclick="aprovarCliente('{{ $transacao->transacao_id }}')" class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition">
                                    ✅ Aprovar
                                </button>
                                <button onclick="rejeitarCliente('{{ $transacao->transacao_id }}')" class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition">
                                    ❌ Rejeitar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Nenhum cadastro pendente
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Últimas Avaliações -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">📋 Últimas Avaliações</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feedback</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ultimasAvaliacoes as $avaliacao)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $avaliacao->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $avaliacao->tenant ? $avaliacao->tenant->nome_empresa : 'Inexistente' }}</td>
                        <td class="px-6 py-4 text-lg">{{ $avaliacao->stars }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $avaliacao->feedback ?: '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($avaliacao->tipo_contato == 'whatsapp')
                                📱 WhatsApp
                            @elseif($avaliacao->tipo_contato == 'line')
                                💬 LINE
                            @else
                                ❌ Não
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($avaliacao->resolvido)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">✅ Resolvido</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">⏳ Pendente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Nenhuma avaliação ainda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function aprovarCliente(transacaoId) {
        if (!confirm('Confirmar aprovação do cliente? O sistema será ativado.')) return;
        
        const response = await fetch('/admin/aprovar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ transacao_id: transacaoId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Cliente aprovado! E-mail de boas-vindas enviado.');
            location.reload();
        } else {
            alert('Erro: ' + result.error);
        }
    }
    
    async function rejeitarCliente(transacaoId) {
        if (!confirm('Rejeitar este cadastro?')) return;
        
        const response = await fetch('/admin/rejeitar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ transacao_id: transacaoId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Cadastro rejeitado.');
            location.reload();
        }
    }
</script>
@endsection
