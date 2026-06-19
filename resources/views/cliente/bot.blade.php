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
                    <svg id="accordion-bot-chevron" class="w-24 h-24 text-neutral-secondary transition transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                
                <!-- Accordion Body -->
                <div id="accordion-bot" class="p-24 space-y-24 hidden">
                    <!-- Language Selector Tabs (dynamic based on pack_idioma) -->
                    <div class="flex gap-8 mb-16">
                        @foreach($localeData as $i => $tab)
                        <button type="button"
                                id="tab-{{ $tab['key'] }}"
                                onclick="switchLanguage('{{ $tab['key'] }}')"
                                class="px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8
                                       {{ $i === 0 ? 'bg-brand-50 text-brand-600 border-brand-200' : 'bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg' }}">
                            <span>{{ $tab['flag'] }} {{ $tab['label'] }}</span>
                            <span class="text-legend px-8 py-2 rounded {{ $i === 0 ? 'bg-brand-100 text-brand-700' : 'bg-gray-100 text-neutral-secondary' }}">Idioma {{ $i + 1 }}</span>
                        </button>
                        @endforeach
                    </div>

                    @php
                    $stepsList = [
                        'general' => [
                            'title' => 'Fluxo Geral (Etapa Inicial)',
                            'icon'  => '<svg class="w-12 h-12 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                            'keys'  => ['welcome' => 'Mensagem de boas-vindas'],
                            'options' => [],
                        ],
                        'positive' => [
                            'title' => 'Fluxo positivo (4-5★)',
                            'icon'  => '<svg class="w-12 h-12 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 00.458 1.258l2.9 3.5a1 1 0 001.536-1.246l-.884-2.783A1 1 0 0110.966 16h4.567a2 2 0 001.99-1.849l.5-8a2 2 0 00-1.99-2.151h-4.567a1 1 0 01-.966-.743l-.884-2.783a1 1 0 00-1.536-1.246l-2.9 3.5A2 2 0 006 10.333z"/></svg>',
                            'keys'  => [
                                'highRate'     => 'Agradecimento (Nota Alta)',
                                'q_recommend'  => 'Convite para avaliação no Google',
                                'highFinalMsg' => 'Encerramento Positivo',
                            ],
                            'options' => [
                                'q_recommend' => [
                                    'pt' => ['⭐ Botão: Avaliar no Google'],
                                    'ja' => ['⭐ ボタン: Googleで評価する'],
                                    'en' => ['⭐ Button: Review on Google'],
                                ],
                            ],
                        ],
                        'negative' => [
                            'title' => 'Fluxo negativo (1-3★)',
                            'icon'  => '<svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-.458-1.258l-2.9-3.5a1 1 0 00-1.536 1.246l.884 2.783A1 1 0 019.034 4H4.467a2 2 0 00-1.99 1.849l-.5 8a2 2 0 001.99 2.151h4.567a1 1 0 01.966.743l.884 2.783a1 1 0 001.536 1.246l2.9-3.5a2 2 0 00.458-3.075z"/></svg>',
                            'keys'  => [
                                'lowRate'          => 'Reconhecimento (Nota Baixa)',
                                'lowRateQ'         => 'Pergunta: O que poderia melhorar?',
                                'q_optional_text'  => 'Pergunta: Gostaria de dar mais detalhes?',
                                'feedback_sent'    => 'Confirmação: Feedback encaminhado ao responsável',
                                'q_contact'        => 'Pergunta: Deseja que a empresa entre em contato?',
                                'lowFinalMsg'      => 'Encerramento Negativo',
                            ],
                            'options' => [
                                'lowRateQ' => [
                                    'pt' => ['😕 Atendimento', '⚙️ Produto ou Serviço', '💸 Preço', '⏱️ Demora', '❗ Outro'],
                                    'ja' => ['😕 接客', '⚙️ 商品またはサービス', '💸 価格', '⏱️ 待ち時間', '❗ その他'],
                                    'en' => ['😕 Service', '⚙️ Product or Service', '💸 Price', '⏱️ Wait time', '❗ Other'],
                                ],
                                'q_optional_text' => [
                                    'pt' => ['✍️ Digite sua mensagem...', '[Enviar]', '[Pular]'],
                                    'ja' => ['✍️ メッセージを入力してください...', '[送信]', '[スキップ]'],
                                    'en' => ['✍️ Type your message...', '[Send]', '[Skip]'],
                                ],
                                'q_contact' => [
                                    'pt' => ['📱 Sim', '❌ Não', '📱 WhatsApp', '📧 E-mail'],
                                    'ja' => ['📱 はい', '❌ いいえ', '💬 LINE', '📧 E-mail'],
                                    'en' => ['📱 Yes', '❌ No', '💬 LINE', '📧 E-mail'],
                                ],
                            ],
                        ],
                    ];
                    @endphp

                    {{-- Forms dinâmicos: um por idioma do pack --}}
                    @foreach($localeData as $i => $tab)
                    <div id="form-{{ $tab['key'] }}" class="space-y-16 {{ $i > 0 ? 'hidden' : '' }}">
                        @foreach($stepsList as $sectionKey => $section)
                            <div class="bg-gray-50 border border-neutral-border p-16 rounded-xl">
                                <div class="flex items-center gap-8 mb-16">
                                    <div class="w-24 h-24 rounded-full bg-neutral-bg flex items-center justify-center flex-shrink-0">
                                        {!! $section['icon'] !!}
                                    </div>
                                    <h3 class="text-body-m font-bold text-neutral-primary">
                                        {{ $section['title'] }}
                                        <span class="text-legend font-normal text-neutral-secondary ml-4">{{ $tab['flag'] }}</span>
                                    </h3>
                                </div>

                                <div class="space-y-16">
                                    @foreach($section['keys'] as $key => $label)
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-12">
                                                <input type="number" min="1"
                                                       name="messages[{{ $tab['locale'] }}][{{ $key }}][step]"
                                                       value="{{ old('messages.'.$tab['locale'].'.'.$key.'.step', $tab['messages'][$key]['step'] ?? '') }}"
                                                       class="w-48 border border-neutral-border rounded-lg py-8 text-body-m text-center font-bold focus:ring-2 focus:ring-brand-600 focus:outline-none"
                                                       placeholder="Off">
                                                <span class="text-neutral-secondary font-bold">-</span>
                                                <input type="text"
                                                       name="messages[{{ $tab['locale'] }}][{{ $key }}][text]"
                                                       value="{{ old('messages.'.$tab['locale'].'.'.$key.'.text', $tab['messages'][$key]['text'] ?? '') }}"
                                                       class="flex-1 border border-neutral-border rounded-lg px-12 py-8 text-body-m focus:ring-2 focus:ring-brand-600 focus:outline-none"
                                                       placeholder="{{ $label }}"
                                                       required>
                                            </div>

                                            @if(isset($section['options'][$key][$tab['locale']]))
                                                <div class="pl-[80px] flex flex-wrap gap-8 items-center">
                                                    <span class="text-[11px] text-neutral-secondary font-bold uppercase tracking-wider">Opções:</span>
                                                    @foreach($section['options'][$key][$tab['locale']] as $opt)
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
                    @endforeach

                    <!-- Collapsible: Motivos de problema -->
                    <div class="border border-neutral-border rounded-xl p-16">
                        <div class="flex items-center gap-8 cursor-pointer select-none" onclick="toggleProblems()">
                            <div class="w-24 h-24 rounded-full bg-neutral-secondary/10 text-neutral-secondary flex items-center justify-center">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-body-m font-bold text-neutral-primary">Motivos de problema</h3>
                            <svg id="chevron-prob" class="w-16 h-16 ml-auto transition transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path></svg>
                        </div>
                        <div id="panel-problems" class="mt-12 space-y-12 hidden">
                            <p class="text-legend text-neutral-secondary/60 font-semibold uppercase tracking-wider mb-8">Marque os problemas que os clientes podem selecionar no bot:</p>
                            
                            <div class="grid grid-cols-2 gap-12 pt-8">
                                @php
                                $allProblems = [
                                    'atendimento' => '😕 Atendimento',
                                    'produto_servico' => '⚙️ Produto ou Serviço',
                                    'preco' => '💸 Preço',
                                    'demora' => '⏱️ Demora',
                                    'limpeza' => '🧹 Limpeza',
                                    'conforto' => '🪑 Conforto',
                                    'entrega' => '📦 Entrega',
                                    'outro' => '❗ Outro'
                                ];
                                $selectedProblems = $cliente->motivos_problema ?? ['atendimento', 'produto_servico', 'preco', 'demora', 'outro'];
                                @endphp
                                
                                @foreach($allProblems as $key => $label)
                                    <label class="flex items-center gap-8 cursor-pointer select-none">
                                        <input type="checkbox" name="motivos_problema[]" value="{{ $key }}" class="rounded text-brand-600 focus:ring-brand-500 border-neutral-border w-16 h-16" {{ in_array($key, $selectedProblems) ? 'checked' : '' }}>
                                        <span class="text-body-m font-medium text-neutral-secondary">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
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
                            <!-- Photo Icon -->
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.9 2.9m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375.375 0 01.75 0z" />
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

                    <!-- Cor Principal -->
                    @php $corAtual = $cliente->cor_principal ?? '#7C3AED'; @endphp
                    <div class="space-y-8">
                        <label class="block text-body-m font-bold text-neutral-secondary">Cor Principal do Chatbot</label>
                        <input type="hidden" name="cor_principal" id="cor-principal-input" value="{{ $corAtual }}">
                        <button type="button" onclick="openColorModal()"
                                class="w-full flex items-center gap-16 bg-gray-50 border border-neutral-border p-14 rounded-xl hover:bg-neutral-bg transition group">
                            <div id="cor-trigger-swatch" class="w-44 h-44 rounded-xl shadow-md border border-black/10 flex-shrink-0 transition-all"
                                 style="background: {{ $corAtual }}"></div>
                            <div class="flex-1 text-left">
                                <span class="block text-body-m font-bold text-neutral-primary font-mono tracking-wide" id="cor-trigger-hex">{{ strtoupper($corAtual) }}</span>
                                <span class="text-legend text-neutral-secondary">Balões, botões e destaques do chatbot</span>
                            </div>
                            <svg class="w-20 h-20 text-neutral-secondary group-hover:text-neutral-primary transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 17.25h.008v.008H6.75v-.008z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @php
            $hiddenKeys = ['q_first_visit', 'first_visit_ack', 'askRate', 'q_period', 'recommend_yes', 'recommend_maybe', 'recommend_no', 'q_optional_photo', 'photo_ack'];
            @endphp
            @foreach($localeData as $tab)
                @foreach($hiddenKeys as $hKey)
                    <input type="hidden" name="messages[{{ $tab['locale'] }}][{{ $hKey }}][step]" value="{{ $tab['messages'][$hKey]['step'] ?? '' }}">
                    <input type="hidden" name="messages[{{ $tab['locale'] }}][{{ $hKey }}][text]" value="{{ $tab['messages'][$hKey]['text'] ?? '' }}">
                @endforeach
            @endforeach

            <!-- Action Button -->
            <button type="submit" class="w-full bg-brand-600 text-white py-16 rounded-xl font-bold hover:bg-brand-700 transition shadow-sm flex items-center justify-center gap-8">
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
                <div id="mock-header" class="relative text-white h-[120px] flex items-center shadow-sm bg-cover bg-center overflow-hidden flex-shrink-0" style="background-color: {{ $corAtual }}; {{ $cliente->cover_path ? 'background-image: url(' . asset('storage/' . $cliente->cover_path) . ');' : '' }}">
                    <!-- Gradient Overlay for Contrast -->
                    <div id="mock-header-overlay" class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/35 to-black/20 z-10 {{ $cliente->cover_path ? '' : 'hidden' }}"></div>

                    <!-- Header Content -->
                    <div class="relative z-20 flex items-center gap-8 p-12 w-full">
                        <div class="w-32 h-32 bg-white border border-black/20 rounded-lg flex items-center justify-center font-bold text-xs overflow-hidden flex-shrink-0" id="mock-logo-box">
                            @if($cliente->logo_path)
                                <img id="mock-logo-img" src="{{ asset('storage/' . $cliente->logo_path) }}" alt="Logo" class="w-full h-full object-contain p-[2px]">
                            @else
                                <span id="mock-logo-emoji" class="text-sm">🏢</span>
                            @endif
                        </div>
                        <div class="leading-none text-left">
                            <span class="block font-bold text-xs tracking-wide truncate max-w-[170px]" id="mock-biz-name" style="text-shadow: 0 1px 4px rgba(0,0,0,0.5)">{{ $cliente->nome_empresa }}</span>
                            <span class="text-[8px] text-white/80 flex items-center gap-4 mt-2 font-medium">
                                <span class="w-4 h-4 rounded-full bg-emerald-400 inline-block animate-pulse"></span> Online
                            </span>
                        </div>
                        <button type="button" class="text-white/60 hover:text-white ml-auto text-xs">✕</button>
                    </div>
                </div>

                <!-- Chat Body (Messages Mock) -->
                <div class="flex-1 p-8 overflow-y-auto space-y-8 flex flex-col justify-start">

                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary text-left" id="preview-msg-welcome">
                            Olá! Como foi sua visita hoje?
                        </div>
                    </div>

                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none p-8 flex justify-center gap-4 shadow-sm w-full">
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-amber-400 text-xs">★</span>
                        </div>
                    </div>

                    <!-- User Msg (colorida pela cor principal) -->
                    <div id="mock-user-bubble" class="text-white rounded-2xl rounded-br-none px-12 py-8 text-[11px] self-end max-w-[80%] shadow-sm" style="background: {{ $corAtual }}">
                        ⭐⭐⭐⭐⭐
                    </div>

                    <div class="flex gap-4 items-end max-w-[80%]">
                        <div class="bg-white border border-neutral-border rounded-2xl rounded-bl-none px-12 py-8 text-[11px] shadow-sm text-neutral-primary text-left" id="preview-msg-high">
                            Que ótimo! Que tal deixar sua avaliação no Google?
                        </div>
                    </div>

                    <div class="border border-neutral-border bg-white rounded-xl p-8 max-w-[90%] shadow-sm self-start text-left">
                        <div class="flex items-center gap-8 mb-8">
                            <div class="w-16 h-16 bg-blue-50 rounded flex items-center justify-center text-blue-600 font-bold text-[10px]">G</div>
                            <div class="leading-tight">
                                <span class="block font-bold text-[9px] text-neutral-primary">Avaliar no Google</span>
                                <span class="text-[7px] text-neutral-secondary">Rápido e prático</span>
                            </div>
                        </div>
                        <button type="button" class="w-full bg-blue-500 text-white py-4 rounded font-bold text-[9px]">↗ Abrir Google</button>
                    </div>

                </div>

                <!-- Input footer mock -->
                <div class="bg-white border-t border-neutral-border p-8 flex items-center gap-8 flex-shrink-0">
                    <input type="text" placeholder="Escreva aqui..." class="flex-1 bg-gray-50 border border-neutral-border rounded-full px-12 py-6 text-[9px] outline-none" disabled>
                    <div id="mock-send-btn" class="w-24 h-24 rounded-full text-white flex items-center justify-center flex-shrink-0" style="background: {{ $corAtual }}">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    const OB_LOCALE_DATA = @json($localeData);
    const OB_LOCALE_KEYS = OB_LOCALE_DATA.map(t => t.key);
    let activeLang = OB_LOCALE_KEYS[0] ?? 'locale1';

    function toggleAccordion(id) {
        const target = document.getElementById(id);
        const chevron = document.getElementById(id + '-chevron');
        if (target.classList.contains('hidden')) {
            target.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            target.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    function switchLanguage(lang) {
        activeLang = lang;
        const activeClass   = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-brand-50 text-brand-600 border-brand-200';
        const inactiveClass = 'px-16 py-8 rounded-lg text-body-m font-bold border transition flex items-center gap-8 bg-white text-neutral-secondary border-neutral-border hover:bg-neutral-bg';
        OB_LOCALE_KEYS.forEach(key => {
            const form = document.getElementById('form-' + key);
            const tab  = document.getElementById('tab-' + key);
            if (form) form.classList.toggle('hidden', key !== lang);
            if (tab)  tab.className = key === lang ? activeClass : inactiveClass;
        });
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
        const idx    = OB_LOCALE_KEYS.indexOf(activeLang);
        const locale = idx >= 0 ? OB_LOCALE_DATA[idx].locale : null;
        if (!locale) return;
        const welcomeEl  = document.querySelector(`[name="messages[${locale}][welcome][text]"]`);
        const highRateEl = document.querySelector(`[name="messages[${locale}][highRate][text]"]`);
        if (welcomeEl)  document.getElementById('preview-msg-welcome').textContent = welcomeEl.value;
        if (highRateEl) document.getElementById('preview-msg-high').textContent    = highRateEl.value;
    }

    document.querySelectorAll('[name$="[welcome][text]"], [name$="[highRate][text]"]').forEach(el => {
        el.addEventListener('input', updatePreview);
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

    // ── COLOR PICKER ──────────────────────────────────────────────────────
    const COLOR_PRESETS = [
        '#7C3AED','#6D28D9','#9333EA','#3B82F6','#0EA5E9','#06B6D4',
        '#10B981','#16A34A','#DC2626','#E11D48','#F59E0B','#1F2937'
    ];

    let pendingColor = document.getElementById('cor-principal-input').value || '#7C3AED';

    function openColorModal() {
        pendingColor = document.getElementById('cor-principal-input').value || '#7C3AED';
        updateModalUI(pendingColor);
        document.getElementById('color-picker-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeColorModal() {
        document.getElementById('color-picker-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function selectPresetColor(color) {
        pendingColor = color;
        updateModalUI(color);
    }

    function updateModalUI(color) {
        document.getElementById('modal-color-strip').style.background =
            `linear-gradient(135deg, ${color} 0%, ${adjustBrightness(color, 40)} 100%)`;
        document.getElementById('apply-color-btn').style.background = color;
        document.getElementById('custom-color-input').value = color;
        document.getElementById('custom-color-swatch').style.background = color;
        document.getElementById('custom-hex-input').value = color.toUpperCase();

        document.querySelectorAll('[data-preset-color]').forEach(btn => {
            const selected = btn.dataset.presetColor === color;
            btn.innerHTML = selected
                ? '<span style="color:#fff; font-size:14px; line-height:1; font-weight:700">✓</span>'
                : '';
            btn.style.outline = selected ? `3px solid ${color}` : 'none';
            btn.style.outlineOffset = '2px';
        });
    }

    function applySelectedColor() {
        const color = pendingColor;
        document.getElementById('cor-principal-input').value = color;
        document.getElementById('cor-trigger-swatch').style.background = color;
        document.getElementById('cor-trigger-hex').textContent = color.toUpperCase();
        applyColorToPreview(color);
        closeColorModal();
    }

    function applyColorToPreview(color) {
        const header = document.getElementById('mock-header');
        if (header) header.style.backgroundColor = color;

        const bubble = document.getElementById('mock-user-bubble');
        if (bubble) bubble.style.background = color;

        const sendBtn = document.getElementById('mock-send-btn');
        if (sendBtn) sendBtn.style.background = color;
    }

    // Convert hex to a slightly lighter shade for gradient end
    function adjustBrightness(hex, amount) {
        const r = Math.min(255, parseInt(hex.slice(1,3), 16) + amount);
        const g = Math.min(255, parseInt(hex.slice(3,5), 16) + amount);
        const b = Math.min(255, parseInt(hex.slice(5,7), 16) + amount);
        return '#' + [r,g,b].map(v => v.toString(16).padStart(2,'0')).join('');
    }

    // Custom color picker (native input)
    document.getElementById('custom-color-input').addEventListener('input', function () {
        pendingColor = this.value;
        document.getElementById('custom-color-swatch').style.background = this.value;
        document.getElementById('custom-hex-input').value = this.value.toUpperCase();
        document.getElementById('modal-color-strip').style.background =
            `linear-gradient(135deg, ${this.value} 0%, ${adjustBrightness(this.value, 40)} 100%)`;
        document.getElementById('apply-color-btn').style.background = this.value;
        document.querySelectorAll('[data-preset-color]').forEach(btn => {
            btn.innerHTML = '';
            btn.style.outline = 'none';
        });
    });

    // Hex text input
    document.getElementById('custom-hex-input').addEventListener('input', function () {
        const val = this.value.startsWith('#') ? this.value : '#' + this.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            pendingColor = val;
            document.getElementById('custom-color-input').value = val;
            document.getElementById('custom-color-swatch').style.background = val;
            document.getElementById('modal-color-strip').style.background =
                `linear-gradient(135deg, ${val} 0%, ${adjustBrightness(val, 40)} 100%)`;
            document.getElementById('apply-color-btn').style.background = val;
        }
    });

    // Close on ESC
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeColorModal(); });

    // Initialize preview with saved color
    applyColorToPreview(pendingColor);
</script>

<!-- ── COLOR PICKER MODAL ──────────────────────────────────────────────────────── -->
<div id="color-picker-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-modal="true">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeColorModal()"></div>

    <!-- Card -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-[360px] overflow-hidden mx-16" style="max-height: 90vh; overflow-y:auto">

        <!-- Color preview strip -->
        <div id="modal-color-strip" class="h-[80px] flex items-center justify-center px-24 transition-all duration-200"
             style="background: linear-gradient(135deg, {{ $corAtual }} 0%, {{ strtoupper(sprintf('#%02x%02x%02x', min(255, hexdec(substr($corAtual,1,2))+40), min(255, hexdec(substr($corAtual,3,2))+40), min(255, hexdec(substr($corAtual,5,2))+40))) }} 100%)">
            <span class="text-white font-bold text-base tracking-wide truncate max-w-full" style="text-shadow: 0 2px 8px rgba(0,0,0,0.4)">
                {{ $cliente->nome_empresa }}
            </span>
        </div>

        <div class="p-24">
            <h3 class="text-body-g font-bold text-neutral-primary mb-4">Cor Principal</h3>
            <p class="text-legend text-neutral-secondary mb-20">Escolha a cor de destaque do chatbot — aplicada em balões, botões e cabeçalho.</p>

            <!-- Preset palette -->
            <div class="grid grid-cols-6 gap-10 mb-24">
                @foreach(['#7C3AED','#6D28D9','#9333EA','#3B82F6','#0EA5E9','#06B6D4','#10B981','#16A34A','#DC2626','#E11D48','#F59E0B','#1F2937'] as $preset)
                    <button type="button"
                            data-preset-color="{{ $preset }}"
                            onclick="selectPresetColor('{{ $preset }}')"
                            class="w-44 h-44 rounded-xl shadow-md transition-all duration-150 hover:scale-110 flex items-center justify-center"
                            style="background: {{ $preset }}; {{ $preset === $corAtual ? 'outline:3px solid '.$preset.'; outline-offset:2px' : '' }}">
                        @if($preset === $corAtual)
                            <span style="color:#fff; font-size:14px; font-weight:700">✓</span>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-12 mb-20">
                <div class="flex-1 h-px bg-neutral-border"></div>
                <span class="text-legend text-neutral-secondary font-medium">ou cor personalizada</span>
                <div class="flex-1 h-px bg-neutral-border"></div>
            </div>

            <!-- Custom hex + native color input -->
            <div class="flex items-center gap-12 bg-gray-50 border border-neutral-border rounded-xl p-12 mb-24">
                <div class="relative w-44 h-44 rounded-xl overflow-hidden border border-neutral-border flex-shrink-0 cursor-pointer shadow-sm">
                    <input type="color" id="custom-color-input" value="{{ $corAtual }}"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" style="transform:scale(2)">
                    <div id="custom-color-swatch" class="w-full h-full" style="background: {{ $corAtual }}"></div>
                </div>
                <div class="flex-1">
                    <label class="text-legend text-neutral-secondary font-bold block mb-6">Código hexadecimal</label>
                    <input type="text" id="custom-hex-input" value="{{ strtoupper($corAtual) }}"
                           maxlength="7" placeholder="#000000"
                           class="w-full border border-neutral-border rounded-lg px-10 py-8 text-body-m font-mono tracking-widest uppercase focus:ring-2 focus:outline-none"
                           style="focus-ring-color: {{ $corAtual }}">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-12">
                <button type="button" onclick="closeColorModal()"
                        class="flex-1 border border-neutral-border text-neutral-secondary py-12 rounded-xl font-bold hover:bg-neutral-bg transition text-body-m">
                    Cancelar
                </button>
                <button type="button" id="apply-color-btn" onclick="applySelectedColor()"
                        class="flex-1 py-12 rounded-xl font-bold text-white transition text-body-m shadow-md"
                        style="background: {{ $corAtual }}">
                    Aplicar Cor
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
