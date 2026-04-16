@extends('layouts.app')

@section('title', 'Avaliações Automáticas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-600 to-blue-500">
    <div class="container mx-auto px-4 py-16">
        <div class="text-center text-white mb-12">
            <h1 class="text-5xl font-bold mb-4">⭐ CP Review Care</h1>
            <p class="text-xl opacity-90">Automatize suas avaliações e proteja sua reputação</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @php
                $planos = [
                    ['name' => 'Lite', 'price' => 'R$ 49', 'features' => ['QR Code', '100 avaliações/mês', 'Relatório básico'], 'btn' => 'Contratar Lite'],
                    ['name' => 'Standard', 'price' => 'R$ 97', 'features' => ['QR Code', 'Avaliações ilimitadas', 'Relatório completo', 'Alerta de nota baixa'], 'popular' => true, 'btn' => 'Contratar Standard'],
                    ['name' => 'Premium', 'price' => 'R$ 197', 'features' => ['Tudo do Standard', 'Display físico', 'Suporte 24/7'], 'btn' => 'Contratar Premium']
                ];
            @endphp

            @foreach($planos as $plano)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden {{ $plano['popular'] ?? false ? 'ring-4 ring-yellow-400 transform scale-105' : '' }}">
                @if($plano['popular'] ?? false)
                <div class="bg-yellow-400 text-center py-2 text-sm font-bold">🔥 MAIS POPULAR</div>
                @endif
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-center">{{ $plano['name'] }}</h3>
                    <div class="text-center mt-4">
                        <span class="text-4xl font-bold text-purple-600">{{ $plano['price'] }}</span>
                        <span class="text-gray-500">/mês</span>
                    </div>
                    <ul class="mt-6 space-y-3">
                        @foreach($plano['features'] as $feature)
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    <button onclick="abrirModal('{{ strtolower($plano['name']) }}')" class="w-full mt-8 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                        {{ $plano['btn'] }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 relative">
        <h2 id="modalTitle" class="text-2xl font-bold mb-4">Contratar Plano</h2>
        <form id="contractForm">
            @csrf
            <input type="hidden" id="plano" name="plano">
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nome da Empresa *</label>
                <input type="text" id="empresa" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">E-mail *</label>
                <input type="email" id="email" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">WhatsApp (com DDD) *</label>
                <input type="tel" id="telefone" placeholder="11999999999" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">LINE ID (opcional)</label>
                <input type="text" id="line_id" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Canal de notificação *</label>
                <select id="canal" class="w-full border rounded-lg px-3 py-2">
                    <option value="whatsapp">📱 WhatsApp</option>
                    <option value="email">📧 E-mail</option>
                    <option value="line">💬 LINE</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">País *</label>
                <select id="pais" class="w-full border rounded-lg px-3 py-2">
                    <option value="br">🇧🇷 Brasil</option>
                    <option value="jp">🇯🇵 Japão</option>
                </select>
            </div>
            
            <div id="paymentArea" class="hidden">
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <p class="font-bold mb-2">💰 Pague via PIX</p>
                    <div id="pixCode" class="text-sm bg-white p-2 rounded font-mono break-all"></div>
                    <p class="text-xs text-gray-500 mt-2">Após o pagamento, seu sistema será ativado em até 24h</p>
                </div>
            </div>
            
            <button type="submit" id="submitBtn" class="w-full bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition mt-4">
                Gerar PIX
            </button>
        </form>
        <button onclick="fecharModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">✕</button>
    </div>
</div>

<script>
    let planoSelecionado = '';

    function abrirModal(plano) {
        planoSelecionado = plano;
        document.getElementById('plano').value = plano;
        document.getElementById('modalTitle').innerText = `Contratar Plano ${plano.charAt(0).toUpperCase() + plano.slice(1)}`;
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modal').classList.add('flex');
    }

    function fecharModal() {
        document.getElementById('modal').classList.add('hidden');
        document.getElementById('modal').classList.remove('flex');
        document.getElementById('paymentArea').classList.add('hidden');
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = 'Gerar PIX';
    }

    document.getElementById('contractForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            plano: document.getElementById('plano').value,
            empresa: document.getElementById('empresa').value,
            email: document.getElementById('email').value,
            telefone: document.getElementById('telefone').value,
            line_id: document.getElementById('line_id').value,
            canal: document.getElementById('canal').value,
            pais: document.getElementById('pais').value,
            _token: '{{ csrf_token() }}'
        };

        const response = await fetch('/contratar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('paymentArea').classList.remove('hidden');
            document.getElementById('pixCode').innerText = result.pix_code;
            document.getElementById('submitBtn').innerHTML = '✅ Aguardando pagamento...';
            document.getElementById('submitBtn').disabled = true;
        }
    });

    window.onclick = function(event) {
        if (event.target === document.getElementById('modal')) {
            fecharModal();
        }
    }
</script>
@endsection
