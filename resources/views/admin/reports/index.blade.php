@extends('layouts.admin')

@section('title', 'Central de Relatórios')

@section('header', 'Central de Relatórios Mensais')

@section('content')
<div class="p-8 space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Dashboard de Envio -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <h3 class="text-xl font-black text-gray-900 mb-6">🚀 Disparar Relatórios</h3>
                
                <form action="{{ route('admin.reports.send') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Destinatários</label>
                            <select name="tenant_id" id="tenant_select" class="block w-full rounded-xl border-gray-100 bg-gray-50 font-bold text-sm">
                                <option value="all">Todos os Tenants Ativos</option>
                                @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->nome_empresa }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Período Referência</label>
                            <div class="py-3 px-4 bg-gray-100 rounded-xl text-sm font-bold text-gray-500">
                                {{ now()->subMonth()->format('F Y') }} (Mês Passado)
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-xl font-black text-sm hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                            Enviar Relatórios Agora
                        </button>
                        <button type="button" onclick="previewReport()" class="text-purple-600 font-bold text-sm px-4">
                            👁️ Visualizar Preview
                        </button>
                    </div>
                </form>
            </div>

            <!-- Log de Envios -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h3 class="text-lg font-black text-gray-900">📄 Histórico de Distribuição</h3>
                </div>
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data Envio</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tenant</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Período</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Abertura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-8 py-4 text-xs font-bold text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-8 py-4 font-bold text-gray-900">{{ $log->tenant->nome_empresa }}</td>
                            <td class="px-8 py-4 text-xs font-mono text-gray-400">{{ $log->periodo }}</td>
                            <td class="px-8 py-4 text-xs">
                                <span class="px-2 py-1 rounded-full font-black uppercase text-[9px] {{ $log->status == 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-8 py-4">
                                @if($log->opened_at)
                                <span class="text-blue-600 text-xs font-bold">✓ Lido em {{ $log->opened_at->format('d/m/Y H:i') }}</span>
                                @else
                                <span class="text-gray-300 text-xs font-bold italic">Não lido</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic">Nenhum relatório enviado ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dicas de Sucesso -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h4 class="text-lg font-black mb-4">💡 Dica de Sucesso</h4>
                <p class="text-sm opacity-80 leading-relaxed mb-6">
                    Acompanhar a taxa de abertura dos relatórios ajuda você a identificar quais lojistas estão mais engajados com o CP Review.
                </p>
                <div class="py-4 px-6 bg-white/10 rounded-2xl">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-1">Média de Abertura Sistema</p>
                    <p class="text-2xl font-black leading-none">68.4%</p>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
                <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">📋 Checklist Mensal</h4>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" checked disabled class="mt-1 rounded text-green-500">
                        <p class="text-xs font-bold text-gray-600">Consolidação de avaliações finalizada</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" checked disabled class="mt-1 rounded text-green-500">
                        <p class="text-xs font-bold text-gray-600">Cálculo de NPS automático concluído</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" class="mt-1 rounded text-purple-500">
                        <p class="text-xs font-bold text-gray-600 shadow-purple-50">Disparar para lojistas em Trial</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewReport() {
        const tenantId = document.getElementById('tenant_select').value;
        if (tenantId === 'all') {
            alert('Por favor, selecione um tenant específico para ver o preview.');
            return;
        }
        window.open(`/admin/reports/preview/${tenantId}`, '_blank');
    }
</script>
@endsection
