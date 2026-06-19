@extends('layouts.cliente')

@section('title', 'Personalização do Bot e Página - CP Review')

@section('cliente_content')
<!-- Page Header -->
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Personalização</h2>
    <p class="text-body-m text-neutral-secondary">Configure a identidade visual da página de avaliação e o fluxo de mensagens do bot</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-16 py-12 mb-24 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-16 mb-24 rounded-lg shadow-sm" role="alert">
        <p class="font-bold">Ocorreu um erro:</p>
        <ul class="list-disc list-inside text-body-m mt-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('cliente.perfil.update', $cliente->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Main Content Split (Form Left, Preview Right) -->
    <div class="grid lg:grid-cols-12 gap-32 items-start">
        
        <!-- Left Side: Accordion and Inputs -->
        <div class="lg:col-span-7 space-y-16">
            
            <!-- ACCORDION 1: PERSONALIZAR BOT -->
            <div class="card overflow-hidden border border-neutral-border bg-white rounded-xl shadow-sm">
                <!-- Accordion Header -->
                <div class="flex items-center justify-between p-16 bg-neutral-card hover:bg-neutral-bg/50 cursor-pointer transition border-b border-neutral-border select-none" onclick="toggleAccordion('accordion-bot')">
                    <div class="flex items-center gap-12">
                        <div class="w-32 h-32 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                            <!-- Sliders Icon -->
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-body-g font-bold text-neutral-primary">1. Personalizar o Bot</h3>
                            <p class="text-legend text-neutral-secondary">Ajuste as mensagens automáticas de atendimento por idioma</p>
                        </div>
                    </div>
                    <svg id="accordion-bot-chevron" class="w-24 h-24 text-neutral-secondary transition transform rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                
                <!-- Accordion Body -->
                <div id="accordion-bot" class="p-24 space-y-24">
                    <!-- Language Selector Tabs -->
                    <div class="flex gap-8 mb-16">
                        <button type="button" id="tab-pt" onclick="switchLanguage('pt')" class="px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200">
                            <span>🇧🇷 Português</span>
                            <span class="text-legend bg-brand-100 px-8 py-2 rounded text-brand-700">Idioma 1</span>
                        </button>
                        <button type="button" id="tab-jp" onclick="switchLanguage('jp')" class="px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg">
                            <span>🇯🇵 Japonês</span>
                            <span class="text-legend bg-gray-100 px-8 py-2 rounded text-neutral-secondary">Idioma 2</span>
                        </button>
                    </div>

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
                    <div id="form-pt" class="space-y-16">
                        @foreach($stepsList as $sectionKey => $section)
                            <div class="bg-gray-50 border border-neutral-border p-16 rounded-xl">
                                <div class="flex items-center gap-8 mb-16">
                                    <div class="w-24 h-24 rounded-full bg-neutral-bg flex items-center justify-center flex-shrink-0">
                                        {!! $section['icon'] !!}
                                    </div>
                                    <h3 class="text-body-m font-bold text-neutral-primary">{{ $section['title'] }}</h3>
                                </div>

                                <div class="space-y-16">
                                    @foreach($section['keys'] as $key => $label)
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-12">
                                                <input type="number" min="1" id="in-pt-{{ $key }}-step" name="messages[pt][{{ $key }}][step]" value="{{ old('messages.pt.'.$key.'.step', $messagesPt[$key]['step'] ?? '') }}" class="w-48 border border-neutral-border rounded-lg py-8 text-body-m text-center font-bold focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="Off">
                                                <span class="text-neutral-secondary font-bold">-</span>
                                                <input type="text" id="in-pt-{{ $key }}-text" name="messages[pt][{{ $key }}][text]" value="{{ old('messages.pt.'.$key.'.text', $messagesPt[$key]['text'] ?? '') }}" class="flex-1 border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="{{ $label }}" required>
                                            </div>
                                            
                                            @if(isset($section['options'][$key]))
                                                <div class="pl-60 flex flex-wrap gap-8 items-center">
                                                    <span class="text-[11px] text-neutral-secondary font-bold uppercase tracking-wider">Opções:</span>
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
                    <div id="form-jp" class="space-y-16 hidden">
                        @foreach($stepsList as $sectionKey => $section)
                            <div class="bg-gray-50 border border-neutral-border p-16 rounded-xl">
                                <div class="flex items-center gap-8 mb-16">
                                    <div class="w-24 h-24 rounded-full bg-neutral-bg flex items-center justify-center flex-shrink-0">
                                        {!! $section['icon'] !!}
                                    </div>
                                    <h3 class="text-body-m font-bold text-neutral-primary">{{ $section['title'] }} (JP)</h3>
                                </div>

                                <div class="space-y-16">
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
                    <div class="border border-neutral-border rounded-xl p-16">
                        <div class="flex items-center gap-8 cursor-pointer select-none" onclick="toggleProblems()">
                            <div class="w-24 h-24 rounded-full bg-neutral-secondary/10 text-neutral-secondary flex items-center justify-center">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-body-m font-bold text-neutral-primary">Motivos de problema</h3>
                            <svg id="chevron-prob" class="w-16 h-16 ml-auto transition transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path></svg>
                        </div>
                        <div id="panel-problems" class="mt-12 space-y-8 hidden">
                            <p class="text-legend text-neutral-secondary leading-relaxed">
                                Os motivos de problemas ajudam os clientes insatisfeitos a categorizar a reclamação (ex: Limpeza, Atendimento, Comida fria, Tempo de espera). Eles são carregados automaticamente pelo bot de acordo com o idioma selecionado pelo cliente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION 2: PERSONALIZAR PÁGINA -->
            <div class="card overflow-hidden border border-neutral-border bg-white rounded-xl shadow-sm">
                <!-- Accordion Header -->
                <div class="flex items-center justify-between p-16 bg-neutral-card hover:bg-neutral-bg/50 cursor-pointer transition border-b border-neutral-border select-none" onclick="toggleAccordion('accordion-page')">
                    <div class="flex items-center gap-12">
                        <div class="w-32 h-32 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                            <!-- Paint Brush / Page design Icon -->
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-3.078 0L2.25 18.374v1.875c0 .414.336.75.75.75h18a.75.75 0 0 0 .75-.75V18.37a3 3 0 0 0-3.078 0L14.47 16.122a3 3 0 0 0-3.078 0l-1.86 1.077zM9.53 16.122V12.75a3 3 0 0 0-3-3H4.5m4.5 3.372v-1.122a3 3 0 0 0-3-3H4.5m10.5 4.122V12.75a3 3 0 0 1 3-3h1.875" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-body-g font-bold text-neutral-primary">2. Personalizar a Página</h3>
                            <p class="text-legend text-neutral-secondary">Adicione o link do Google, logotipo e imagem de capa do estabelecimento</p>
                        </div>
                    </div>
                    <svg id="accordion-page-chevron" class="w-24 h-24 text-neutral-secondary transition transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                
                <!-- Accordion Body -->
                <div id="accordion-page" class="p-24 space-y-24 hidden">
                    <!-- Google Maps Link -->
                    <div>
                        <label class="block text-body-m font-bold text-neutral-secondary mb-4">Link de Avaliação do Google</label>
                        <input type="url" name="google_maps_link" id="google-maps-link-input" value="{{ old('google_maps_link', $cliente->google_maps_link) }}" class="w-full border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none" placeholder="https://g.page/r/.../review">
                        <p class="text-legend text-neutral-secondary/60 mt-4 font-medium">Usado para redirecionar clientes com avaliações de nota 4-5★ para avaliar sua empresa diretamente no Google.</p>
                    </div>
                    
                    <!-- File Uploads (Logo & Cover) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-24">
                        <!-- Logo Upload -->
                        <div class="space-y-8">
                            <label class="block text-body-m font-bold text-neutral-secondary">Logotipo da Empresa</label>
                            <div class="flex items-center gap-16 bg-gray-50 border border-neutral-border p-12 rounded-xl">
                                <div class="w-48 h-48 border border-neutral-border rounded-lg flex items-center justify-center bg-white overflow-hidden shadow-sm flex-shrink-0" id="logo-preview-box">
                                    @if($cliente->logo_path)
                                        <img src="{{ asset('storage/' . $cliente->logo_path) }}" alt="Logo" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-title-1" id="logo-preview-emoji">🏢</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="logo-file-input" name="logo" accept="image/jpeg,image/png,image/webp" class="hidden">
                                    <button type="button" onclick="document.getElementById('logo-file-input').click()" class="border border-neutral-border hover:bg-neutral-bg text-neutral-primary px-12 py-6 rounded-lg text-body-m font-bold transition">
                                        Escolher Imagem
                                    </button>
                                    <p class="text-[10px] text-neutral-secondary/60 mt-4">PNG, JPG ou WEBP. Quadrada (máx. 2MB).</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Upload -->
                        <div class="space-y-8">
                            <label class="block text-body-m font-bold text-neutral-secondary">Banner de Capa</label>
                            <div class="flex items-center gap-16 bg-gray-50 border border-neutral-border p-12 rounded-xl">
                                <div class="w-64 h-48 border border-neutral-border rounded-lg flex items-center justify-center bg-white overflow-hidden shadow-sm flex-shrink-0 bg-cover bg-center" id="cover-preview-box" style="{{ $cliente->cover_path ? 'background-image: url(' . asset('storage/' . $cliente->cover_path) . ')' : '' }}">
                                    @if(!$cliente->cover_path)
                                        <span class="text-legend text-neutral-secondary/40 font-bold" id="cover-preview-text">Sem Capa</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="cover-file-input" name="cover" accept="image/jpeg,image/png,image/webp" class="hidden">
                                    <button type="button" onclick="document.getElementById('cover-file-input').click()" class="border border-neutral-border hover:bg-neutral-bg text-neutral-primary px-12 py-6 rounded-lg text-body-m font-bold transition">
                                        Escolher Imagem
                                    </button>
                                    <p class="text-[10px] text-neutral-secondary/60 mt-4">PNG, JPG ou WEBP. Retangular (máx. 5MB).</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

            <!-- Action Button -->
            <button type="submit" class="w-full bg-brand-600 text-white py-16 rounded-xl font-bold hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-8">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Salvar Alterações de Personalização
            </button>
        </div>

        <!-- Right Side: Phone Preview (PWA Simulator) -->
        <div class="lg:col-span-5 flex flex-col items-center">
            <span class="text-body-m font-bold text-neutral-secondary mb-16 self-start uppercase tracking-wider">Preview do Chatbot (PWA)</span>
            
            <!-- Mobile body frame -->
            <div class="w-[305px] h-[550px] border-[8px] border-neutral-primary rounded-[36px] overflow-hidden shadow-2xl flex flex-col bg-gray-50 relative select-none">
                <!-- Camera Notch -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 w-48 h-12 bg-neutral-primary rounded-full z-50"></div>

                <!-- Chat Header (Cover Banner) -->
                <div id="mock-header" class="relative text-white h-[120px] flex items-end shadow-sm bg-cover bg-center overflow-hidden flex-shrink-0" style="background-color: #7C3AED; {{ $cliente->cover_path ? 'background-image: url(' . asset('storage/' . $cliente->cover_path) . ');' : '' }}">
                    <!-- Gradient Overlay for Contrast -->
                    <div id="mock-header-overlay" class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/35 to-black/20 z-10 {{ $cliente->cover_path ? '' : 'hidden' }}"></div>

                    <!-- Header Content -->
                    <div class="relative z-20 flex items-center gap-8 p-12 w-full mt-12">
                        <div class="w-32 h-32 bg-white border border-white/20 rounded-lg flex items-center justify-center text-brand-600 font-bold text-xs overflow-hidden flex-shrink-0" id="mock-logo-box">
                            @if($cliente->logo_path)
                                <img id="mock-logo-img" src="{{ asset('storage/' . $cliente->logo_path) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <span id="mock-logo-emoji" class="text-sm">🏢</span>
                            @endif
                        </div>
                        <div class="leading-none text-left">
                            <span class="block font-bold text-xs tracking-wide truncate max-w-[170px]" id="mock-biz-name">{{ $cliente->nome_empresa }}</span>
                            <span class="text-[8px] text-brand-100/90 flex items-center gap-4 mt-2 font-medium">
                                <span class="w-4 h-4 rounded-full bg-emerald-400 inline-block animate-pulse"></span> Online
                            </span>
                        </div>
                        
                        <!-- Close button ✕ -->
                        <button type="button" class="text-brand-100 hover:text-white ml-auto text-xs opacity-60">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Chat Body (Messages Mock) -->
                <div class="flex-1 p-8 overflow-y-auto space-y-8 flex flex-col justify-start">
                    
                    <!-- Bot Msg 1 (Boas-vindas) -->
                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary text-left" id="preview-msg-welcome">
                            Olá! Como foi sua visita hoje?
                        </div>
                    </div>

                    <!-- Bot Msg Star rating input mock -->
                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none p-8 flex justify-center gap-4 shadow-sm w-full">
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                        </div>
                    </div>

                    <!-- User Msg (5 Stars selected) -->
                    <div class="bg-brand-600 text-white rounded-2xl rounded-br-none px-12 py-8 text-[11px] self-end max-w-[80%] shadow-sm flex items-center">
                        ⭐⭐⭐⭐⭐
                    </div>

                    <!-- Bot Msg 2 (Convite Google) -->
                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary text-left" id="preview-msg-high">
                            Que ótimo! Que tal deixar sua avaliação no Google?
                        </div>
                    </div>

                    <!-- Redirect Google Card -->
                    <div class="border border-neutral-border bg-white rounded-xl p-8 max-w-[90%] shadow-sm self-start text-left">
                        <div class="flex items-center gap-8 mb-8">
                            <div class="w-16 h-16 bg-blue-50 rounded flex items-center justify-center text-blue-600 font-bold text-[10px]">
                                G
                            </div>
                            <div class="leading-tight">
                                <span class="block font-bold text-[9px] text-neutral-primary">Avaliar no Google</span>
                                <span class="text-[7px] text-neutral-secondary">Rápido e prático</span>
                            </div>
                        </div>
                        <button type="button" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-4 rounded font-bold text-[9px] flex items-center justify-center gap-2">
                            ↗ Abrir Google
                        </button>
                    </div>

                </div>

                <!-- Input footer mock -->
                <div class="bg-white border-t border-neutral-border p-8 flex items-center gap-8 flex-shrink-0">
                    <input type="text" placeholder="Escreva aqui..." class="flex-1 bg-gray-50 border border-neutral-border rounded-full px-12 py-6 text-[9px] outline-none" disabled>
                    <div class="w-24 h-24 rounded-full bg-brand-600 text-white flex items-center justify-center flex-shrink-0">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    let activeLang = 'pt';

    // Accordion handler
    function toggleAccordion(id) {
        const botBody = document.getElementById('accordion-bot');
        const botChevron = document.getElementById('accordion-bot-chevron');
        const pageBody = document.getElementById('accordion-page');
        const pageChevron = document.getElementById('accordion-page-chevron');
        
        if (id === 'accordion-bot') {
            botBody.classList.remove('hidden');
            botChevron.classList.add('rotate-180');
            pageBody.classList.add('hidden');
            pageChevron.classList.remove('rotate-180');
        } else {
            pageBody.classList.remove('hidden');
            pageChevron.classList.add('rotate-180');
            botBody.classList.add('hidden');
            botChevron.classList.remove('rotate-180');
        }
    }

    function switchLanguage(lang) {
        activeLang = lang;
        if (lang === 'pt') {
            document.getElementById('form-pt').classList.remove('hidden');
            document.getElementById('form-jp').classList.add('hidden');
            document.getElementById('tab-pt').className = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200';
            document.getElementById('tab-jp').className = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg';
        } else {
            document.getElementById('form-pt').classList.add('hidden');
            document.getElementById('form-jp').classList.remove('hidden');
            document.getElementById('tab-pt').className = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg';
            document.getElementById('tab-jp').className = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200';
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

    // Attach real-time preview updates for text inputs
    const textInputIds = ['pt-welcome-text', 'pt-highRate-text', 'jp-welcome-text', 'jp-highRate-text'];
    textInputIds.forEach(id => {
        const el = document.getElementById(`in-${id}`);
        if (el) {
            el.addEventListener('input', updatePreview);
        }
    });

    // Real-time Preview for Logo File Upload
    document.getElementById('logo-file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                // Update Thumbnail Box
                const previewBox = document.getElementById('logo-preview-box');
                previewBox.innerHTML = `<img src="${evt.target.result}" alt="Logo" class="w-full h-full object-cover">`;
                
                // Update Mobile Mockup
                const mockBox = document.getElementById('mock-logo-box');
                mockBox.innerHTML = `<img id="mock-logo-img" src="${evt.target.result}" alt="Logo" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Real-time Preview for Cover File Upload
    document.getElementById('cover-file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                // Update Thumbnail Box
                const previewBox = document.getElementById('cover-preview-box');
                previewBox.style.backgroundImage = `url(${evt.target.result})`;
                const previewText = document.getElementById('cover-preview-text');
                if (previewText) previewText.style.display = 'none';
                
                // Update Mobile Mockup
                const mockHeader = document.getElementById('mock-header');
                mockHeader.style.backgroundImage = `url(${evt.target.result})`;
                document.getElementById('mock-header-overlay').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Initialize preview values
    updatePreview();
</script>
@endsection
