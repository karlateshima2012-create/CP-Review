@extends('layouts.app')

@section('title', 'Avaliações Automáticas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-600 to-blue-500">
    <!-- Header / Navbar -->
    <nav class="container mx-auto px-4 py-6 flex justify-between items-center relative z-10">
        <div class="text-white text-2xl font-bold flex items-center gap-2">
            <span class="text-3xl">⭐</span> CP Review Care
        </div>
        <div>
            <a href="{{ url('/login') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white px-6 py-2 rounded-full font-semibold border border-white/30 transition-all">
                Entrar
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-16">
        <div class="text-center text-white mb-12">
            <h1 class="text-5xl font-bold mb-4">Seu feedback é o seu faturamento</h1>
            <p class="text-xl opacity-90">Gestão inteligente de reputação para negócios de alto nível.</p>
        </div>

        <!-- 🚀 CONSOLE DE TESTE RÁPIDO (Temporário) -->
        <div class="max-w-4xl mx-auto mb-12 rounded-2xl bg-white/10 backdrop-blur-md p-6 border border-white/20 shadow-2xl">
            <h2 class="text-white text-lg font-bold mb-4 flex items-center justify-center gap-2">
                🛠️ Painel de Testes
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ url('/admin/login') }}" target="_blank" class="flex flex-col items-center p-4 bg-white rounded-xl hover:bg-purple-100 transition-all transform hover:-translate-y-1 shadow-lg group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition-transform">👤</span>
                    <span class="font-bold text-gray-800">Admin</span>
                    <span class="text-[10px] text-gray-500 uppercase font-semibold">Gerenciar SaaS</span>
                </a>
                <a href="{{ url('/login') }}" target="_blank" class="flex flex-col items-center p-4 bg-white rounded-xl hover:bg-purple-100 transition-all transform hover:-translate-y-1 shadow-lg group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition-transform">🏪</span>
                    <span class="font-bold text-gray-800">Lojista</span>
                    <span class="text-[10px] text-gray-500 uppercase font-semibold">Painel da Loja</span>
                </a>
                <a href="{{ url('/avaliar/sabor-senior') }}" target="_blank" class="flex flex-col items-center p-4 bg-white rounded-xl hover:bg-purple-100 transition-all transform hover:-translate-y-1 shadow-lg group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition-transform">📱</span>
                    <span class="font-bold text-gray-800">Cliente</span>
                    <span class="text-[10px] text-gray-500 uppercase font-semibold">Ver o Bot</span>
                </a>
            </div>
            <div class="mt-4 flex flex-wrap justify-center gap-x-6 gap-y-2 text-white/80 text-xs">
                <div>🔑 <b>Admin:</b> admin@cpreview.com / admin123</div>
                <div>🔑 <b>Lojista:</b> loja@teste.com / loja123</div>
            </div>
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
