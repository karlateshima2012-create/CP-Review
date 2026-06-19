@extends('layouts.app')

@section('title', 'CP Review Care - Gestão Inteligente')

@section('content')
<div class="min-h-screen bg-neutral-bg text-neutral-primary font-sans selection:bg-brand-600 selection:text-white flex flex-col">
    <!-- Navbar -->
    <nav class="container mx-auto px-6 py-6 flex justify-between items-center bg-transparent relative z-10">
        <div class="flex items-center gap-2">
            <img src="/logo.svg" alt="CP Review Logo" class="h-10">
        </div>
        <div>
            <a href="{{ url('/login') }}" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-md hover:shadow-lg">
                Entrar
            </a>
        </div>
    </nav>

    <!-- SECTION 1: HERO -->
    <header class="flex-grow flex items-center pt-16 pb-24 border-b border-neutral-border bg-white">
        <div class="container mx-auto px-6 text-center max-w-4xl">
            <h1 class="text-display-h1 font-bold mb-6 text-neutral-primary font-display uppercase tracking-wide leading-tight">
                Seu feedback é o seu <br/> <span class="text-brand-600">faturamento</span>
            </h1>
            <p class="text-body-g md:text-title-3 text-neutral-secondary mx-auto mb-10 leading-relaxed max-w-2xl">
                O CP Review Care é a plataforma inteligente que transforma críticas privadas em oportunidades de fidelização. Capture feedbacks negativos antes que cheguem à internet e incentive avaliações positivas no Google.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#testar" class="bg-brand-600 hover:bg-brand-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-transform hover:scale-105 shadow-xl shadow-brand-200">
                    Ver como funciona
                </a>
                <a href="#planos" class="bg-white border border-neutral-border hover:bg-neutral-bg text-neutral-primary px-8 py-4 rounded-xl font-bold text-lg transition-all">
                    Ver Planos
                </a>
            </div>
        </div>
    </header>

    <!-- SECTION 2: TESTE A EXPERIÊNCIA -->
    <section id="testar" class="py-24 bg-neutral-bg border-b border-neutral-border">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-title-1 md:text-display-h1 font-bold mb-6 text-neutral-primary font-display uppercase tracking-wide">
                Teste a Experiência do Cliente
            </h2>
            <p class="text-body-g text-neutral-secondary max-w-2xl mx-auto mb-12">
                Aponte a câmera do seu celular para o QR Code abaixo e simule a jornada de um cliente no seu estabelecimento. Rapidez e fluidez sem nenhuma instalação.
            </p>

            <div class="flex justify-center">
                <div class="bg-white p-8 rounded-3xl border border-neutral-border shadow-2xl flex flex-col items-center transform transition duration-500 hover:scale-105">
                    <p class="text-sm font-bold uppercase tracking-widest text-brand-600 mb-6 flex items-center gap-2">
                        📱 Scanner de Teste
                    </p>
                    <div class="p-6 bg-white rounded-2xl shadow-inner mb-6 border border-brand-100">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&color=7C3AED&bgcolor=FFFFFF&data={{ urlencode(url('/avaliar/creative-print')) }}" alt="QR Code" class="w-64 h-64 rounded-lg">
                    </div>
                    
                    <a href="{{ url('/avaliar/creative-print') }}" target="_blank" class="mb-6 flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-200">
                        <span>🚀 Simular no Navegador</span>
                    </a>

                    <p class="text-body-m text-neutral-secondary font-medium w-64 text-center">
                        Acesse direto ou aponte a câmera para simular no celular.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: CONTRATAR -->
    <section id="contratar" class="py-24 bg-white border-b border-neutral-border">
        <div class="container mx-auto px-6 max-w-2xl">
            <div class="text-center mb-12">
                <h2 class="text-title-1 md:text-display-h1 font-bold text-neutral-primary font-display uppercase tracking-wide">
                    Comece agora
                </h2>
                <p class="text-body-g text-neutral-secondary mt-4">
                    Preencha os dados e nossa equipe ativa seu painel em até 24h.
                </p>
            </div>

            <div class="bg-white border border-neutral-border rounded-3xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-brand-600 to-indigo-700 px-8 py-6">
                    <h3 class="text-white font-bold text-lg">Solicitar acesso ao CP Review</h3>
                    <p class="text-white/70 text-sm mt-1">Você receberá as credenciais por e-mail após a confirmação.</p>
                </div>

                <form id="contratar-form" class="p-8 space-y-8">
                    @csrf

                    @include('partials._cliente-fields', ['defaults' => []])

                    {{-- Plano --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Plano</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['value' => 'standard', 'label' => 'Standard', 'price' => '¥4.800'],
                                ['value' => 'pro',      'label' => 'Pro',      'price' => '¥7.800'],
                                ['value' => 'elite',    'label' => 'Elite',    'price' => '¥12.000'],
                            ] as $plano)
                            <label for="plano-{{ $plano['value'] }}" class="cursor-pointer">
                                <input type="radio" id="plano-{{ $plano['value'] }}" name="plano" value="{{ $plano['value'] }}"
                                       {{ $plano['value'] === 'standard' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="border-2 rounded-2xl p-4 text-center transition-all
                                            peer-checked:border-brand-600 peer-checked:bg-brand-50
                                            border-gray-200 hover:border-gray-300">
                                    <p class="font-bold text-sm text-neutral-primary">{{ $plano['label'] }}</p>
                                    <p class="text-xs text-neutral-secondary mt-1">{{ $plano['price'] }}/mês</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" id="contratar-btn"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-brand-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Solicitar acesso
                    </button>
                </form>

                <div id="contratar-success" class="hidden p-8 text-center space-y-4">
                    <div class="text-5xl">🎉</div>
                    <h3 class="text-xl font-bold text-neutral-primary">Solicitação recebida!</h3>
                    <p class="text-neutral-secondary text-sm">Nossa equipe ativará seu painel e enviará as credenciais de acesso para o e-mail informado em até 24h.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.getElementById('contratar-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = document.getElementById('contratar-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg> Enviando...';

        try {
            var data = new FormData(this);
            var res = await fetch('/contratar', { method: 'POST', body: data });
            var json = await res.json();
            if (json.success) {
                document.getElementById('contratar-form').classList.add('hidden');
                document.getElementById('contratar-success').classList.remove('hidden');
            } else {
                btn.disabled = false;
                btn.innerHTML = 'Solicitar acesso';
                alert('Erro ao enviar. Tente novamente.');
            }
        } catch(err) {
            btn.disabled = false;
            btn.innerHTML = 'Solicitar acesso';
            alert('Erro ao enviar. Tente novamente.');
        }
    });
    </script>

    <!-- RODA PE -->
    <footer class="bg-white py-12">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <img src="/logo.svg" alt="CP Review" class="h-8 grayscale opacity-50">
            <p class="text-neutral-secondary text-sm">
                &copy; 2026 Creative Print. Todos os direitos reservados.
            </p>
            <div class="flex gap-4">
                <a href="#" class="text-neutral-secondary hover:text-brand-600 transition-colors">Termos</a>
                <a href="#" class="text-neutral-secondary hover:text-brand-600 transition-colors">Privacidade</a>
            </div>
        </div>
    </footer>
</div>
@endsection
