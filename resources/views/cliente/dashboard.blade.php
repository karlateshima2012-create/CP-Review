@extends('layouts.app')

@section('title', 'Painel do Cliente')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-purple-600">CP Review Care</h1>
            <span class="text-gray-600">{{ $cliente->nome_empresa }}</span>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-purple-600">{{ $totalAvaliacoes }}</div>
                <div class="text-gray-500">Total Avaliações</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-yellow-500">{{ number_format($mediaNotas, 1) }}⭐</div>
                <div class="text-gray-500">Média de Notas</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-red-500">{{ $negativas }}</div>
                <div class="text-gray-500">Avaliações Negativas</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-3xl font-bold text-green-500">{{ $cliente->avaliacoes()->where('resolvido', true)->count() }}</div>
                <div class="text-gray-500">Resolvidas</div>
            </div>
        </div>

        <!-- BI Insights -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-600">
                <h3 class="text-lg font-bold mb-4">👥 Insights de Operação</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Novos Clientes</span>
                            <span class="font-bold">{{ number_format($pctPrimeiraVisita, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $pctPrimeiraVisita }}%"></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Volume por Período</h4>
                        <div class="flex gap-2">
                            @foreach($periodos as $p)
                                <div class="flex-1 bg-gray-50 p-2 rounded text-center border">
                                    <div class="text-xs text-gray-400">{{ $p->periodo_visita }}</div>
                                    <div class="font-bold">{{ $p->total }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        <!-- QR Code -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <h2 class="text-xl font-bold mb-4">📱 Seu QR Code</h2>
                <img src="{{ url("/cliente/qrcode/{$cliente->id}") }}" alt="QR Code" class="mx-auto w-48 h-48">
                <div class="mt-4">
                    <a href="{{ url("/cliente/qrcode/{$cliente->id}/download") }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 inline-block">
                        ⬇️ Baixar QR Code
                    </a>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                    Link direto: <a href="{{ $cliente->url_avaliacao }}" class="text-purple-600 break-all" target="_blank">{{ $cliente->url_avaliacao }}</a>
                </p>
            </div>

            <!-- Configuração do Bot -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">🤖 Configuração do "Bot" Multilíngue</h2>
                <form action="{{ route('cliente.perfil.update', $cliente) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Versão BR -->
                        <div class="border-b pb-4">
                            <h3 class="text-sm font-bold text-purple-600 uppercase mb-3">🇧🇷 Versão em Português</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Boas-vindas</label>
                                    <input type="text" name="msg_boas_vindas_br" value="{{ $cliente->msg_boas_vindas_br }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Pergunta da Nota</label>
                                    <input type="text" name="msg_pergunta_nota_br" value="{{ $cliente->msg_pergunta_nota_br }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Nota Alta</label>
                                    <input type="text" name="msg_agradecimento_alta_br" value="{{ $cliente->msg_agradecimento_alta_br }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Nota Baixa</label>
                                    <input type="text" name="msg_agradecimento_baixa_br" value="{{ $cliente->msg_agradecimento_baixa_br }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                            </div>
                        </div>

                        <!-- Versão JP -->
                        <div>
                            <h3 class="text-sm font-bold text-red-600 uppercase mb-3">🇯🇵 Versão em Japonês (Keigo)</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Welcome (Boas-vindas)</label>
                                    <input type="text" name="msg_boas_vindas_jp" value="{{ $cliente->msg_boas_vindas_jp }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Rating Question</label>
                                    <input type="text" name="msg_pergunta_nota_jp" value="{{ $cliente->msg_pergunta_nota_jp }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">High Rating</label>
                                    <input type="text" name="msg_agradecimento_alta_jp" value="{{ $cliente->msg_agradecimento_alta_jp }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Low Rating</label>
                                    <input type="text" name="msg_agradecimento_baixa_jp" value="{{ $cliente->msg_agradecimento_baixa_jp }}" class="w-full border rounded-lg px-3 py-2 text-sm mt-1">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold hover:bg-purple-700 transition">
                            Salvar Todas as Versões
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Últimas Avaliações -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">📋 Últimas Avaliações</h2>
            </div>
            <div class="divide-y">
                @forelse($ultimasAvaliacoes as $avaliacao)
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-2xl">{{ $avaliacao->stars }}</div>
                            <div class="flex gap-1 mt-1">
                               @if($avaliacao->primeira_visita)
                                    <span class="bg-blue-50 text-blue-600 text-[10px] px-2 py-0.5 rounded">Nova Visita</span>
                               @endif
                               <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded">{{ $avaliacao->periodo_visita }}</span>
                            </div>
                            <p class="text-gray-600 mt-2">{{ $avaliacao->feedback ?: 'Sem feedback escrito' }}</p>
                            <div class="flex gap-2 items-center mt-2">
                                @if($avaliacao->problema)
                                    <span class="inline-block bg-red-50 text-red-600 text-sm px-2 py-1 rounded">{{ $avaliacao->problema }}</span>
                                @endif
                                @if($avaliacao->foto_problema)
                                    <a href="{{ Storage::url($avaliacao->foto_problema) }}" target="_blank" class="text-purple-600 text-sm underline flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Ver Foto
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ $avaliacao->created_at->format('d/m/Y H:i') }}</div>
                            @if($avaliacao->resolvido)
                                <span class="inline-block bg-green-100 text-green-600 text-sm px-2 py-1 rounded mt-1">✅ Resolvido</span>
                            @else
                                <button onclick="abrirResponderModal({{ $avaliacao->id }})" class="bg-blue-500 text-white text-sm px-3 py-1 rounded mt-1 hover:bg-blue-600">
                                    Responder
                                </button>
                            @endif
                        </div>
                    </div>
                    @if($avaliacao->resposta_dono)
                    <div class="mt-3 bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700">Sua resposta:</p>
                        <p class="text-gray-600">{{ $avaliacao->resposta_dono }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Nenhuma avaliação recebida ainda.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Responder -->
<div id="responderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold mb-4">Responder Avaliação</h2>
        <form id="responderForm">
            @csrf
            <input type="hidden" id="avaliacao_id">
            <textarea id="resposta" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Digite sua resposta..." required></textarea>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">Enviar</button>
                <button type="button" onclick="fecharResponderModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirResponderModal(id) {
        document.getElementById('avaliacao_id').value = id;
        document.getElementById('responderModal').classList.remove('hidden');
        document.getElementById('responderModal').classList.add('flex');
    }

    function fecharResponderModal() {
        document.getElementById('responderModal').classList.add('hidden');
        document.getElementById('responderModal').classList.remove('flex');
    }

    document.getElementById('responderForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('avaliacao_id').value;
        const resposta = document.getElementById('resposta').value;
        
        const response = await fetch(`/cliente/avaliacao/${id}/responder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ resposta })
        });
        
        if (response.ok) {
            location.reload();
        }
    });
</script>
@endsection
