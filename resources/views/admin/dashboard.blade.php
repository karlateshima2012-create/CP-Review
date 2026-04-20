@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Super Admin: Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- MRR -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl shadow-lg p-6 text-white border-none">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest opacity-80">Receita Recorrente (MRR)</span>
                <span class="bg-white/20 p-2 rounded-lg">💰</span>
            </div>
            <div class="text-3xl font-black">¥ {{ number_format($mrr, 0) }}</div>
            <div class="text-[10px] mt-2 opacity-60">Consolidado de tenants ativos</div>
        </div>

        <!-- Tenants Stats -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Breakdown Tenants</span>
                <span class="bg-blue-50 p-2 rounded-lg text-blue-600">🏢</span>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $clientesStatus['ativos'] }}</div>
                    <div class="text-[10px] text-green-500 font-bold uppercase">Ativos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800">{{ $clientesStatus['trial'] }}</div>
                    <div class="text-[10px] text-blue-500 font-bold uppercase">Trial</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800">{{ $clientesStatus['inativos'] }}</div>
                    <div class="text-[10px] text-red-400 font-bold uppercase">Inativos</div>
                </div>
            </div>
        </div>

        <!-- Avaliações Stats -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Performance Bot</span>
                <span class="bg-yellow-50 p-2 rounded-lg text-yellow-600">⚡</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $avaliacoesHoje }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Hoje</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $avaliacoesMes }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Este Mês</div>
                </div>
            </div>
        </div>

        <!-- Média Geral -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Satisfação Global</span>
                <span class="bg-indigo-50 p-2 rounded-lg text-indigo-600">⭐</span>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($mediaNotas, 1) }}</div>
            <div class="text-[10px] mt-1 text-gray-400 uppercase font-bold tracking-tighter">NPS Médio Consolidado</div>
        </div>
    </div>

    @if(count($trialsExpirando) > 0 || count($alertasFalha) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Trial Alerts -->
        @if(count($trialsExpirando) > 0)
        <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100">
            <h3 class="text-orange-800 font-bold text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                ⚠️ Trial Expirando (Próximos 7 dias)
            </h3>
            <div class="space-y-3">
                @foreach($trialsExpirando as $c)
                <div class="flex items-center justify-between bg-white/60 p-3 rounded-xl border border-orange-200">
                    <div>
                        <div class="text-sm font-bold text-orange-900">{{ $c->nome_empresa }}</div>
                        <div class="text-[10px] text-orange-700">Expira em {{ $c->trial_ends_at->format('d/m') }} ({{ $c->trial_ends_at->diffForHumans() }})</div>
                    </div>
                    <a href="{{ route('admin.clientes.edit', $c->id) }}" class="text-xs bg-orange-200 text-orange-800 px-3 py-1.5 rounded-lg font-bold">Converter</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Failure Alerts -->
        <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
            <h3 class="text-red-800 font-bold text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                🚨 Falhas em Notificações
            </h3>
            @forelse($alertasFalha as $falha)
                <!-- Loop de falhas aqui -->
            @empty
            <div class="flex items-center justify-center h-24 text-red-300 text-sm italic">
                Nenhuma falha crítica detectada nas últimas 24h.
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- Charts and Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500 mb-6">Crescimento de Tenants (Semanal)</h3>
            <div class="h-64">
                <canvas id="tenantsChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center text-center">
            <div class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Meta de Crescimento</div>
            <div class="text-4xl font-black text-purple-600 mb-2">
                {{ round(($clientesStatus['ativos'] / 100) * 100) }}%
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                <div class="bg-purple-600 h-full" style="width: {{ ($clientesStatus['ativos'] / 100) * 100 }}%"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-4 leading-tight">Progresso rumo aos primeiros 100 tenants ativos.</p>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Novos Tenants
    const ctx = document.getElementById('tenantsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartTenants->map(fn($c) => 'Semana ' . $c->week)) !!},
            datasets: [{
                label: 'Novos Tenants',
                data: {!! json_encode($chartTenants->pluck('total')) !!},
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

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
