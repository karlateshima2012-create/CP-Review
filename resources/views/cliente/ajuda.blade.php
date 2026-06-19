@extends('layouts.cliente')

@section('title', 'Guia de Uso - CP Review')

@section('cliente_content')
<div class="mb-32">
    <h2 class="text-title-1 font-bold text-neutral-primary">Guia de Uso</h2>
    <p class="text-body-m text-neutral-secondary">Tudo que você precisa saber para tirar o máximo do CP Review</p>
</div>

<div class="max-w-3xl space-y-24">

    {{-- ── VISÃO GERAL ─────────────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-20 h-20 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h3 class="text-body-g font-bold text-neutral-primary">Como o CP Review funciona</h3>
        </div>
        <p class="text-body-m text-neutral-secondary leading-relaxed mb-16">
            O CP Review é um sistema de gestão de avaliações que separa o feedback positivo do negativo antes de chegar ao Google. Clientes satisfeitos são direcionados ao Google; insatisfeitos ficam registrados internamente para que você resolva antes que se tornem resenhas públicas.
        </p>
        <div class="grid grid-cols-3 gap-12 text-center">
            <div class="bg-neutral-bg rounded-xl p-16">
                <div class="text-2xl mb-8">📱</div>
                <p class="text-body-m font-bold text-neutral-primary">Cliente escaneia o QR Code</p>
            </div>
            <div class="bg-neutral-bg rounded-xl p-16">
                <div class="text-2xl mb-8">⭐</div>
                <p class="text-body-m font-bold text-neutral-primary">Bot pergunta a nota (1–5)</p>
            </div>
            <div class="bg-neutral-bg rounded-xl p-16">
                <div class="text-2xl mb-8">🔀</div>
                <p class="text-body-m font-bold text-neutral-primary">4–5★ vai ao Google · 1–3★ fica em Ocorrências</p>
            </div>
        </div>
    </div>

    {{-- ── PASSO 1: DIVULGAÇÃO ─────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <span class="text-body-g font-bold text-emerald-600">1</span>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Divulgue seu link de avaliação</h3>
                <p class="text-legend text-neutral-secondary">Aba Divulgação</p>
            </div>
        </div>

        <div class="space-y-12">
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">🖨️</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Imprima o QR Code</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Baixe o QR Code e imprima em tamanho visível. Cole na mesa, no balcão, no cardápio ou na vitrine. Quanto mais visível, mais avaliações você recebe.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">📲</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Compartilhe o link digitalmente</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Use os botões de WhatsApp, LINE ou e-mail para enviar o link diretamente para seus clientes após o atendimento.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">🔗</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Copie a mensagem pronta</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">O botão "Copiar link" já inclui o texto <em>"Olá! Por favor, deixe sua avaliação sobre nós em: [link]"</em> — é só colar no WhatsApp.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PASSO 2: BOT ────────────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="text-body-g font-bold text-blue-600">2</span>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">O bot recebe o cliente</h3>
                <p class="text-legend text-neutral-secondary">Automático — nenhuma ação necessária</p>
            </div>
        </div>

        <p class="text-body-m text-neutral-secondary leading-relaxed mb-16">
            Quando o cliente acessa o link, um chatbot conversa com ele automaticamente. O fluxo varia de acordo com a nota dada:
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-12">
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-16">
                <p class="text-body-m font-bold text-emerald-700 mb-8">⭐ Nota 4 ou 5 (Positivo)</p>
                <ol class="text-body-m text-emerald-800 space-y-4 list-decimal list-inside leading-relaxed">
                    <li>Mensagem de agradecimento</li>
                    <li>Convite para avaliar no Google</li>
                    <li>Encerramento com mensagem final</li>
                </ol>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-16">
                <p class="text-body-m font-bold text-red-700 mb-8">⚠️ Nota 1, 2 ou 3 (Negativo)</p>
                <ol class="text-body-m text-red-800 space-y-4 list-decimal list-inside leading-relaxed">
                    <li>Reconhecimento do problema</li>
                    <li>Pergunta: o que poderia melhorar?</li>
                    <li>Opção de enviar mais detalhes</li>
                    <li>Pergunta: deseja ser contatado?</li>
                    <li>Encerramento — vai para Ocorrências</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- ── PASSO 3: OCORRÊNCIAS ────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                <span class="text-body-g font-bold text-orange-600">3</span>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Gerencie as Ocorrências</h3>
                <p class="text-legend text-neutral-secondary">Aba Ocorrências</p>
            </div>
        </div>

        <p class="text-body-m text-neutral-secondary leading-relaxed mb-16">
            Toda avaliação negativa (1–3★) gera uma ocorrência. Você recebe uma notificação e pode acompanhar tudo pelo painel.
        </p>

        <div class="space-y-12">
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">📋</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Anotação interna</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Use para registrar o que foi feito internamente: "Conversei com o cliente por telefone", "Devolução realizada", "Encaminhado para a gerência". Essa anotação <strong>não é visível para o cliente</strong>.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">✅</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Marcar como resolvido</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Após resolver o problema, marque a ocorrência como resolvida. Isso move o card da aba <em>Pendentes</em> para <em>Resolvidas</em> e mantém seu histórico organizado.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">🔄</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Reabrir ocorrência</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Se o problema voltar ou não foi resolvido de fato, use o botão "Reabrir" para mover a ocorrência de volta para Pendentes.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PASSO 4: PERSONALIZAÇÃO ─────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <span class="text-body-g font-bold text-purple-600">4</span>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Personalize o bot e a página</h3>
                <p class="text-legend text-neutral-secondary">Aba Personalização</p>
            </div>
        </div>

        <div class="space-y-12">
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">💬</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Mensagens do bot</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Edite cada mensagem do fluxo de conversa. O número à esquerda é a ordem de exibição. Deixe em branco o número para desativar uma mensagem.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">🖼️</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Logo e capa</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Faça upload do logo e de uma imagem de capa para a página de avaliação. O logo aparece no cabeçalho do chat; a capa é o banner de fundo.</p>
                </div>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">🎨</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Cor principal</p>
                    <p class="text-body-m text-neutral-secondary leading-relaxed">Escolha a cor que representa sua marca. Ela aparece nos botões e destaques da página de avaliação.</p>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-12 flex gap-10">
                <span class="text-amber-500 flex-shrink-0 mt-1">💡</span>
                <p class="text-body-m text-amber-800 leading-relaxed">Após salvar as alterações do bot, <strong>abra a página de avaliação em uma aba nova</strong> para ver as mudanças (ou use Ctrl+Shift+R para forçar o recarregamento).</p>
            </div>
        </div>
    </div>

    {{-- ── PASSO 5: GOOGLE ─────────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-20 h-20 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.4-1.04 2.58-2.23 3.37v2.79h3.61c2.11-1.95 3.26-4.82 3.26-8.17z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.61-2.79c-.98.66-2.23 1.06-3.67 1.06-2.82 0-5.21-1.9-6.07-4.47H2.18v2.87C4.01 20.07 7.77 23 12 23z" fill="#34A853"/>
                    <path d="M5.93 14.14A7.01 7.01 0 0 1 5.56 12c0-.74.13-1.46.37-2.14V7.99H2.18A11.01 11.01 0 0 0 1 12c0 1.79.43 3.48 1.18 4.99l3.75-2.85z" fill="#FBBC05"/>
                    <path d="M12 5.04c1.59 0 3.01.55 4.13 1.62l3.08-3.08C17.46 1.96 14.97 1 12 1 7.77 1 4.01 3.93 2.18 8.01l3.75 2.85C6.79 6.94 9.18 5.04 12 5.04z" fill="#EA4335"/>
                </svg>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Responda avaliações no Google</h3>
                <p class="text-legend text-neutral-secondary">Google Business Profile</p>
            </div>
        </div>

        <p class="text-body-m text-neutral-secondary leading-relaxed mb-16">
            Responder avaliações no Google — tanto as positivas quanto as negativas — melhora seu posicionamento nas buscas e demonstra profissionalismo para futuros clientes.
        </p>

        <div class="space-y-12">
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">1️⃣</span>
                <p class="text-body-m text-neutral-secondary leading-relaxed">Acesse <strong>business.google.com/reviews</strong> e faça login com a conta Google do seu negócio.</p>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">2️⃣</span>
                <p class="text-body-m text-neutral-secondary leading-relaxed">Encontre a avaliação que deseja responder e clique em <strong>"Responder"</strong>.</p>
            </div>
            <div class="flex gap-12">
                <span class="text-xl flex-shrink-0">3️⃣</span>
                <p class="text-body-m text-neutral-secondary leading-relaxed">Escreva uma resposta personalizada. Para avaliações positivas: agradeça e reforce um diferencial. Para negativas: reconheça o problema, peça desculpas e informe o que foi feito.</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-lg p-12 flex gap-10 mt-16">
            <span class="text-blue-500 flex-shrink-0 mt-1">💡</span>
            <p class="text-body-m text-blue-800 leading-relaxed">Responda todas as avaliações em até 48 horas. O Google valoriza negócios que interagem com seus clientes.</p>
        </div>
    </div>

    {{-- ── DASHBOARD ───────────────────────────────────────────────────────── --}}
    <div class="card p-24">
        <div class="flex items-center gap-12 mb-16">
            <div class="w-36 h-36 rounded-xl bg-neutral-bg flex items-center justify-center flex-shrink-0">
                <svg class="w-20 h-20 text-neutral-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
            </div>
            <div>
                <h3 class="text-body-g font-bold text-neutral-primary">Acompanhe pelo Dashboard</h3>
                <p class="text-legend text-neutral-secondary">Aba Dashboard</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12">
            <div class="flex gap-10">
                <span class="text-lg flex-shrink-0">📊</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Gráfico de 30 dias</p>
                    <p class="text-legend text-neutral-secondary">Avaliações positivas e negativas por dia</p>
                </div>
            </div>
            <div class="flex gap-10">
                <span class="text-lg flex-shrink-0">📈</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Taxa de conversão</p>
                    <p class="text-legend text-neutral-secondary">Quantos scans viraram avaliações</p>
                </div>
            </div>
            <div class="flex gap-10">
                <span class="text-lg flex-shrink-0">⭐</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Nota média</p>
                    <p class="text-legend text-neutral-secondary">Média geral de todas as avaliações</p>
                </div>
            </div>
            <div class="flex gap-10">
                <span class="text-lg flex-shrink-0">🔔</span>
                <div>
                    <p class="text-body-m font-bold text-neutral-primary">Ocorrências pendentes</p>
                    <p class="text-legend text-neutral-secondary">Alertas de feedbacks negativos não resolvidos</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SUPORTE ─────────────────────────────────────────────────────────── --}}
    <div class="card p-24 bg-brand-50 border border-brand-100">
        <div class="flex items-center gap-12 mb-12">
            <span class="text-2xl">💬</span>
            <h3 class="text-body-g font-bold text-brand-700">Precisa de ajuda?</h3>
        </div>
        <p class="text-body-m text-brand-700 leading-relaxed mb-16">
            Nossa equipe de suporte está disponível para tirar dúvidas, fazer configurações iniciais e ajudar você a extrair o máximo do sistema.
        </p>
        <a href="{{ route('cliente.conta', $cliente->id) }}"
           class="inline-flex items-center gap-8 bg-brand-600 text-white py-10 px-20 rounded-lg font-bold hover:bg-brand-700 transition text-body-m">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
            </svg>
            Falar com suporte
        </a>
    </div>

</div>
@endsection
