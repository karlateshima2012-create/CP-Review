@extends('layouts.cliente')

@section('title', 'Configurar Bot - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Bot de Avaliação</h2>
    <p class="text-body-m text-neutral-secondary">Configure as mensagens em cada idioma</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-16 py-12 mb-24 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('cliente.perfil.update', $cliente->id) }}" method="POST">
    @csrf

    <!-- Language Selector Tabs -->
    <div class="flex gap-8 mb-24">
        <button type="button" id="tab-pt" onclick="switchLanguage('pt')" class="px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200">
            <span>🇧🇷 Português</span>
            <span class="text-legend bg-brand-100 px-8 py-2 rounded text-brand-700">Idioma 1</span>
        </button>
        <button type="button" id="tab-jp" onclick="switchLanguage('jp')" class="px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg">
            <span>🇯🇵 Japonês</span>
            <span class="text-legend bg-gray-100 px-8 py-2 rounded text-neutral-secondary">Idioma 2</span>
        </button>
    </div>

    <!-- Main Content Split (Form Left, Preview Right) -->
    <div class="grid lg:grid-cols-12 gap-32 items-start">
        
        <!-- Left Side: Inputs -->
        <div class="lg:col-span-7 space-y-24">
            
@php
$stepsList = [
    'general' => [
        'title' => 'Fluxo Geral (Etapa Inicial)',
        'icon' => '<svg class="w-12 h-12 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
        'keys' => [
            'welcome' => 'Como foi sua experiência hoje?',
        ],
        'options' => []
    ],
    'positive' => [
        'title' => 'Fluxo positivo (4-5★)',
        'icon' => '<svg class="w-12 h-12 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 00.458 1.258l2.9 3.5a1 1 0 001.536-1.246l-.884-2.783A1 1 0 0110.966 16h4.567a2 2 0 001.99-1.849l.5-8a2 2 0 00-1.99-2.151h-4.567a1 1 0 01-.966-.743l-.884-2.783a1 1 0 00-1.536-1.246l-2.9 3.5A2 2 0 006 10.333z"/></svg>',
        'keys' => [
            'highRate' => 'Agradecimento (Nota Alta)',
            'q_recommend' => 'Você pode deixar uma avaliação rápida no Google?',
            'highFinalMsg' => 'Encerramento Positivo',
        ],
        'options' => [
            'q_recommend' => ['pt' => ['⭐ Botão: Avaliar no Google'], 'ja' => ['⭐ ボタン: Googleで評価する']],
        ]
    ],
    'negative' => [
        'title' => 'Fluxo negativo (1-3★)',
        'icon' => '<svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-.458-1.258l-2.9-3.5a1 1 0 00-1.536 1.246l.884 2.783A1 1 0 019.034 4H4.467a2 2 0 00-1.99 1.849l-.5 8a2 2 0 001.99 2.151h4.567a1 1 0 01.966.743l.884 2.783a1 1 0 001.536 1.246l2.9-3.5a2 2 0 00.458-3.075z"/></svg>',
        'keys' => [
            'lowRate' => 'Agradecimento (Nota Baixa)',
            'lowRateQ' => 'Pergunta: O que mais impactou sua experiência?',
            'q_optional_text' => 'Pergunta: Gostaria de nos contar mais detalhes?',
            'q_contact' => 'Pergunta: Deseja que a empresa entre em contato?',
            'lowFinalMsg' => 'Encerramento Negativo',
        ],
        'options' => [
            'lowRateQ' => ['pt' => ['😕 Atendimento', '⚙️ Produto ou Serviço', '💸 Preço', '⏱️ Demora', '❗ Outro'], 'ja' => ['😕 接客', '⚙️ 商品またはサービス', '💸 価格', '⏱️ 待ち時間', '❗ その他']],
            'q_optional_text' => ['pt' => ['✍️ Digite sua mensagem...', '[Enviar]', '[Pular]'], 'ja' => ['✍️ メッセージを入力してください...', '[送信]', '[スキップ]']],
            'q_contact' => ['pt' => ['📱 Sim', '❌ Não', '📱 WhatsApp', '📧 E-mail'], 'ja' => ['📱 はい', '❌ いいえ', '💬 LINE', '📧 E-mail']],
        ]
    ]
];
@endphp

            <!-- PORTUGUESE FORM -->
            <div id="form-pt" class="space-y-24">
                @foreach($stepsList as $sectionKey => $section)
                    <div class="card p-24">
                        <div class="flex items-center gap-8 mb-16">
                            <div class="w-24 h-24 rounded-full bg-neutral-bg flex items-center justify-center">
                                {!! $section['icon'] !!}
                            </div>
                            <h3 class="text-body-g font-bold text-neutral-primary">{{ $section['title'] }}</h3>
                        </div>

                        <div class="space-y-20">
                            @foreach($section['keys'] as $key => $label)
                                <div class="space-y-6">
                                    <div class="flex items-center gap-12">
                                        <input type="number" min="1" id="in-pt-{{ $key }}-step" name="messages[pt][{{ $key }}][step]" value="{{ old('messages.pt.'.$key.'.step', $messagesPt[$key]['step'] ?? '') }}" class="w-48 border border-neutral-border rounded-lg py-8 text-body-m text-center font-bold focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Off">
                                        <span class="text-neutral-secondary font-bold">-</span>
                                        <input type="text" id="in-pt-{{ $key }}-text" name="messages[pt][{{ $key }}][text]" value="{{ old('messages.pt.'.$key.'.text', $messagesPt[$key]['text'] ?? '') }}" class="flex-1 border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="{{ $label }}" required>
                                    </div>
                                    
                                    @if(isset($section['options'][$key]))
                                        <div class="pl-60 flex flex-wrap gap-8 items-center">
                                            <span class="text-[11px] text-neutral-secondary font-bold uppercase tracking-wider">Opções de resposta:</span>
                                            @foreach($section['options'][$key]['pt'] as $opt)
                                                <span class="text-legend bg-neutral-bg border border-neutral-border px-8 py-2 rounded text-neutral-secondary font-semibold">{{ $opt }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- JAPANESE FORM -->
            <div id="form-jp" class="space-y-24 hidden">
                @foreach($stepsList as $sectionKey => $section)
                    <div class="card p-24">
                        <div class="flex items-center gap-8 mb-16">
                            <div class="w-24 h-24 rounded-full bg-neutral-bg flex items-center justify-center">
                                {!! $section['icon'] !!}
                            </div>
                            <h3 class="text-body-g font-bold text-neutral-primary">{{ $section['title'] }} (JP)</h3>
                        </div>

                        <div class="space-y-20">
                            @foreach($section['keys'] as $key => $label)
                                <div class="space-y-6">
                                    <div class="flex items-center gap-12">
                                        <input type="number" min="1" id="in-jp-{{ $key }}-step" name="messages[ja][{{ $key }}][step]" value="{{ old('messages.ja.'.$key.'.step', $messagesJp[$key]['step'] ?? '') }}" class="w-48 border border-neutral-border rounded-lg py-8 text-body-m text-center font-bold focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Off">
                                        <span class="text-neutral-secondary font-bold">-</span>
                                        <input type="text" id="in-jp-{{ $key }}-text" name="messages[ja][{{ $key }}][text]" value="{{ old('messages.ja.'.$key.'.text', $messagesJp[$key]['text'] ?? '') }}" class="flex-1 border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="{{ $label }}" required>
                                    </div>
                                    
                                    @if(isset($section['options'][$key]))
                                        <div class="pl-60 flex flex-wrap gap-8 items-center">
                                            <span class="text-[11px] text-neutral-secondary font-bold uppercase tracking-wider">返答オプション:</span>
                                            @foreach($section['options'][$key]['ja'] as $opt)
                                                <span class="text-legend bg-neutral-bg border border-neutral-border px-8 py-2 rounded text-neutral-secondary font-semibold">{{ $opt }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Collapsible: Motivos de problema -->
            <div class="card p-24">
                <div class="flex items-center gap-8 cursor-pointer" onclick="toggleProblems()">
                    <div class="w-24 h-24 rounded-full bg-neutral-secondary/10 text-neutral-secondary flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-body-g font-bold text-neutral-primary">Motivos de problema</h3>
                    <svg id="chevron-prob" class="w-16 h-16 ml-auto transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path></svg>
                </div>
                <div id="panel-problems" class="mt-16 space-y-12 hidden">
                    <p class="text-body-m text-neutral-secondary leading-relaxed">
                        Os motivos de problemas ajudam os clientes insatisfeitos a categorizar a reclamação (ex: Limpeza, Atendimento, Comida fria, Tempo de espera). Eles são carregados automaticamente pelo bot de acordo com o idioma selecionado pelo cliente.
                    </p>
                </div>
            </div>

            @php
            $hiddenKeys = ['q_first_visit', 'first_visit_ack', 'askRate', 'q_period', 'recommend_yes', 'recommend_maybe', 'recommend_no', 'q_optional_photo', 'photo_ack'];
            @endphp
            @foreach($hiddenKeys as $hKey)
                <input type="hidden" name="messages[pt][{{ $hKey }}][step]" value="{{ $messagesPt[$hKey]['step'] ?? '' }}">
                <input type="hidden" name="messages[pt][{{ $hKey }}][text]" value="{{ $messagesPt[$hKey]['text'] ?? '' }}">
                <input type="hidden" name="messages[ja][{{ $hKey }}][step]" value="{{ $messagesJp[$hKey]['step'] ?? '' }}">
                <input type="hidden" name="messages[ja][{{ $hKey }}][text]" value="{{ $messagesJp[$hKey]['text'] ?? '' }}">
            @endforeach

            <button type="submit" class="w-full bg-brand-600 text-white py-16 rounded-lg font-bold hover:bg-brand-700 transition">
                ✓ Salvar alterações
            </button>
        </div>

        <!-- Right Side: Phone Preview -->
        <div class="lg:col-span-5 flex flex-col items-center">
            <span class="text-body-m font-bold text-neutral-secondary mb-16 self-start">PREVIEW DO BOT</span>
            
            <!-- Mobile body frame -->
            <div class="w-[300px] h-[550px] border-[8px] border-neutral-primary rounded-[32px] overflow-hidden shadow-2xl flex flex-col bg-gray-50 relative">
                <!-- Camera Notch -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 w-48 h-12 bg-neutral-primary rounded-full z-50"></div>

                <!-- Chat Header -->
                <div class="bg-brand-600 text-white pt-16 pb-8 px-12 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-8 mt-4">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-brand-600 font-bold text-xs">
                            S
                        </div>
                        <div class="leading-tight">
                            <span class="block font-bold text-xs">{{ $cliente->nome_empresa }}</span>
                            <span class="text-[9px] text-brand-100 flex items-center gap-4">
                                <span class="w-4 h-4 rounded-full bg-emerald-400 inline-block"></span> Online
                            </span>
                        </div>
                    </div>
                    <button type="button" class="text-brand-100 hover:text-white mt-4">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Chat Body (Messages) -->
                <div class="flex-1 p-8 overflow-y-auto space-y-8 flex flex-col justify-start">
                    
                    <!-- Bot Msg 1 (Boas-vindas) -->
                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary" id="preview-msg-welcome">
                            Olá! Como foi sua visita hoje?
                        </div>
                    </div>

                    <!-- Bot Msg Star rating input mock -->
                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none p-8 flex justify-center gap-4 shadow-sm">
                            <span class="text-amber-400 text-xs">⭐</span>
                            <span class="text-amber-400 text-xs">⭐</span>
                            <span class="text-amber-400 text-xs">⭐</span>
                            <span class="text-amber-400 text-xs">⭐</span>
                            <span class="text-amber-400 text-xs">⭐</span>
                        </div>
                    </div>

                    <!-- User Msg (5 Stars selected) -->
                    <div class="bg-brand-600 text-white rounded-2xl rounded-br-none px-12 py-8 text-[11px] self-end max-w-[80%] shadow-sm flex items-center">
                        ⭐⭐⭐⭐⭐
                    </div>

                    <!-- Bot Msg 2 (Convite Google) -->
                    <div class="flex gap-4 items-end max-w-[80%] animate-[fadeIn_0.5s_ease-out]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary" id="preview-msg-high">
                            Que ótimo! Que tal deixar sua avaliação no Google?
                        </div>
                    </div>

                    <!-- Redirect Google Card -->
                    <div class="border border-neutral-border bg-white rounded-xl p-8 max-w-[90%] shadow-sm self-start">
                        <div class="flex items-center gap-8 mb-8">
                            <div class="w-24 h-24 bg-blue-50 rounded flex items-center justify-center text-blue-600 font-bold text-[10px]">
                                G
                            </div>
                            <div class="leading-tight">
                                <span class="block font-bold text-[10px] text-neutral-primary">Avaliar no Google</span>
                                <span class="text-[8px] text-neutral-secondary">Leva menos de 1 minuto</span>
                            </div>
                        </div>
                        <button type="button" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-6 rounded font-bold text-[10px] flex items-center justify-center gap-4">
                            ↗ Abrir Google
                        </button>
                    </div>

                </div>

                <!-- Input footer mock -->
                <div class="bg-white border-t border-neutral-border p-8 flex items-center gap-8">
                    <input type="text" placeholder="Escreva aqui..." class="flex-1 bg-gray-50 border border-neutral-border rounded-full px-12 py-6 text-[10px] outline-none" disabled>
                    <div class="w-24 h-24 rounded-full bg-brand-600 text-white flex items-center justify-center">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    let activeLang = 'pt';

    function switchLanguage(lang) {
        activeLang = lang;
        if (lang === 'pt') {
            document.getElementById('form-pt').classList.remove('hidden');
            document.getElementById('form-jp').classList.add('hidden');
            document.getElementById('tab-pt').className = 'px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200';
            document.getElementById('tab-jp').className = 'px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg';
        } else {
            document.getElementById('form-pt').classList.add('hidden');
            document.getElementById('form-jp').classList.remove('hidden');
            document.getElementById('tab-pt').className = 'px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg';
            document.getElementById('tab-jp').className = 'px-16 py-10 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200';
        }
        updatePreview();
    }

    function toggleProblems() {
        const panel = document.getElementById('panel-problems');
        const chevron = document.getElementById('chevron-prob');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    function updatePreview() {
        const welcomeText = document.getElementById(`in-${activeLang}-welcome-text`).value;
        const highText = document.getElementById(`in-${activeLang}-highRate-text`).value;

        document.getElementById('preview-msg-welcome').textContent = welcomeText;
        document.getElementById('preview-msg-high').textContent = highText;
    }

    // Attach real-time preview updates
    const inputs = ['pt-welcome-text', 'pt-highRate-text', 'jp-welcome-text', 'jp-highRate-text'];
    inputs.forEach(id => {
        document.getElementById(`in-${id}`).addEventListener('input', updatePreview);
    });

    // Initialize preview values
    updatePreview();
</script>
@endsection
