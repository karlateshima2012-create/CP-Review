@extends('layouts.app')

@section('title', 'CP Review Care - Gestão Inteligente')

@section('content')
<div class="min-h-screen bg-neutral-bg text-neutral-primary font-sans selection:bg-brand-600 selection:text-white flex flex-col">
    <!-- Navbar -->
    <nav class="container mx-auto px-6 py-6 flex justify-between items-center bg-transparent relative z-10">
        <div class="flex items-center gap-2">
            <img src="/logo.png" alt="CP Review Logo" class="h-10">
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
                    <div class="p-4 bg-brand-50 rounded-2xl shadow-inner mb-6 border border-brand-100">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&color=7C3AED&bgcolor=F5F3FF&data={{ urlencode(url('/avaliar/creative-print')) }}" alt="QR Code" class="w-48 h-48 rounded-lg mix-blend-multiply">
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

    <!-- RODA PE -->
    <footer class="bg-white py-12">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <img src="/logo.png" alt="CP Review" class="h-8 grayscale opacity-50">
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
