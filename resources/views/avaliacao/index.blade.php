@extends('layouts.app')

@section('title', 'Avaliar Experiência')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col max-w-lg mx-auto shadow-2xl overflow-hidden relative border-x border-gray-200">
    
    <!-- Cabeçalho do Chat -->
    <div class="bg-white p-4 border-b flex items-center gap-3 z-10 shadow-sm">
        <div class="relative">
            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white text-xl">
                <span>⭐</span>
            </div>
            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
        </div>
        <div>
            <h1 class="font-bold text-gray-800 text-lg leading-tight">{{ $cliente->nome_empresa }}</h1>
            <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Assistente Virtual Ativo</p>
        </div>
    </div>

    <!-- Área de Mensagens -->
    <div id="chat-container" class="flex-1 p-4 space-y-4 overflow-y-auto bg-[#F0F2F5] pb-24">
        <!-- As mensagens serão inseridas aqui via JS -->
    </div>

    <!-- Indicador de Digitação -->
    <div id="typing-indicator" class="hidden absolute bottom-24 left-4 z-10">
        <div class="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm flex gap-1">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
        </div>
    </div>

    <!-- Área Fixa de Inputs / Controle -->
    <div id="input-area" class="absolute bottom-0 left-0 right-0 bg-white p-4 border-t shadow-lg-up transition-all duration-300 transform translate-y-full">
        <!-- Controles dinâmicos (estrelas, botões, etc) -->
    </div>

</div>

<style>
    .shadow-lg-up { box-shadow: 0 -4px 15px rgba(0,0,0,0.05); }
    
    .chat-bubble {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 20px;
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 8px;
        position: relative;
        animation: fadeIn 0.3s ease-out;
    }

    .bubble-bot {
        background: white;
        color: #1c1e21;
        border-bottom-left-radius: 4px;
        align-self: flex-start;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .bubble-user {
        background: #6D28D9;
        color: white;
        border-bottom-right-radius: 4px;
        align-self: flex-end;
        align-items: flex-end;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estrelas */
    .star-btn { transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .star-btn:active { transform: scale(0.85); }

    /* Hide scrollbar */
    #chat-container::-webkit-scrollbar { display: none; }
</style>

<script>
    const chatContainer = document.getElementById('chat-container');
    const typingIndicator = document.getElementById('typing-indicator');
    const inputArea = document.getElementById('input-area');

    let dadosAvaliacao = {
        nota: 0,
        problema: null,
        feedback: ''
    };

    const strings = {
        br: {
            boasVindas: "Olá!👋 Seja muito bem-vindo ao suporte de experiência da <b>{{ $cliente->nome_empresa }}</b>.",
            perguntaNota: "Em uma escala de 1 a 5, como você avalia sua visita hoje?✨",
            empatia: "Lamento muito que sua experiência não tenha sido 100% positiva.😔 Queremos entender o que aconteceu...",
            entusiasmo: "Ficamos muito felizes em saber que você gostou!🌟 Pode nos contar o que mais te agradou?",
            agradecimento: "Muito obrigado por sua avaliação! Ela é fundamental para continuarmos evoluindo.🚀"
        },
        jp: {
            boasVindas: "こんにちは！👋 <b>{{ $cliente->nome_empresa }}</b>のカスタマー体験サポートへようこそ。",
            perguntaNota: "本日のご来店はいかがでしたでしょうか？ 5段階（1:不満〜5:満足）でお聞かせください。✨",
            empatia: "ご不便をおかけして申し訳ございません。😔 今後の改善のため、詳しくお聞かせいただけますでしょうか？",
            entusiasmo: "嬉しいお言葉をありがとうございます！🌟 特にどの点にご満足いただけましたか？",
            agradecimento: "貴重なご意見をありがとうございました。🚀 またのご来店を心よりお待ちしております。"
        }
    };

    const lang = (navigator.language.startsWith('ja')) ? 'jp' : 'br';
    const s = strings[lang];

    async function sleep(ms) { return new Promise(resolve => setTimeout(resolve, ms)); }

    function addBubble(text, type = 'bot') {
        const div = document.createElement('div');
        div.className = `chat-bubble ${type === 'bot' ? 'bubble-bot' : 'bubble-user'}`;
        div.innerHTML = text;
        
        const wrapper = document.createElement('div');
        wrapper.className = 'flex flex-col';
        wrapper.appendChild(div);
        
        chatContainer.appendChild(wrapper);
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
    }

    async function showTyping(ms = 800) {
        typingIndicator.classList.remove('hidden');
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        await sleep(ms);
        typingIndicator.classList.add('hidden');
    }

    function showInput(html) {
        inputArea.innerHTML = html;
        inputArea.classList.remove('translate-y-full');
    }

    function hideInput() {
        inputArea.classList.add('translate-y-full');
    }

    // Início do Fluxo
    async function startFlow() {
        await sleep(500);
        await showTyping(1000);
        addBubble(s.boasVindas);
        
        await sleep(400);
        await showTyping(1200);
        addBubble(s.perguntaNota);

        // Mostrar Estrelas
        showInput(`
            <div class="flex justify-between gap-2 max-w-sm mx-auto">
                ${[1, 2, 3, 4, 5].map(i => `
                    <button onclick="setNota(${i})" class="star-btn text-4xl p-2">
                        ${i <= 3 ? '⭐' : '🌟'}
                        <div class="text-[10px] font-bold text-gray-400 mt-1">${i}</div>
                    </button>
                `).join('')}
            </div>
        `);
    }

    window.setNota = async (n) => {
        dadosAvaliacao.nota = n;
        hideInput();
        addBubble(`${n} ${n > 1 ? 'estrelas' : 'estrela'}`, 'user');

        await sleep(600);
        await showTyping(1000);

        if (n <= 3) {
            addBubble(s.empatia);
            const problemas = ['Demora ⏰', 'Atendimento 👤', 'Qualidade 🍽️', 'Limpeza 🧼'];
            showInput(`
                <div class="grid grid-cols-2 gap-2">
                    ${problemas.map(p => `
                        <button onclick="setProblema('${p}')" class="p-3 border rounded-xl hover:bg-purple-50 text-sm font-medium transition-colors">
                            ${p}
                        </button>
                    `).join('')}
                </div>
            `);
        } else {
            addBubble(s.entusiasmo);
            const positivos = ['Rapidez ⚡', 'Sabor 😋', 'Equipe 🤝', 'Ambiente ✨'];
            showInput(`
                <div class="grid grid-cols-2 gap-2">
                    ${positivos.map(p => `
                        <button onclick="setProblema('${p}')" class="p-3 border rounded-xl hover:bg-purple-50 text-sm font-medium transition-colors">
                            ${p}
                        </button>
                    `).join('')}
                </div>
            `);
        }
    };

    window.setProblema = async (p) => {
        dadosAvaliacao.problema = p;
        hideInput();
        addBubble(p, 'user');

        await sleep(600);
        await showTyping(800);
        
        showInput(`
            <div class="space-y-3">
                <textarea id="feedback-text" class="w-full p-4 border rounded-2xl text-sm focus:ring-purple-600 focus:border-purple-600 outline-none" rows="3" placeholder="Quer detalhar algo? (Opcional)"></textarea>
                <button onclick="finalizar()" class="w-full bg-purple-600 text-white py-4 rounded-2xl font-bold shadow-lg transition-transform active:scale-95">
                    Finalizar Avaliação
                </button>
            </div>
        `);
    };

    window.finalizar = async () => {
        dadosAvaliacao.feedback = document.getElementById('feedback-text').value;
        hideInput();
        
        if (dadosAvaliacao.feedback) {
            addBubble(dadosAvaliacao.feedback, 'user');
        }

        await showTyping(1500);
        addBubble(s.agradecimento);

        // Única chamada ao servidor
        fetch("{{ route('avaliacao.salvar', $cliente->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(dadosAvaliacao)
        });

        // Mostrar Redes Sociais se nota alta
        if (dadosAvaliacao.nota >= 4) {
            await sleep(1000);
            await showTyping(800);
            addBubble("Se puder, compartilhe sua alegria também no Google!👇");
            showInput(`
                <div class="flex flex-col gap-2">
                    <a href="https://www.google.com/maps?q={{ urlencode($cliente->nome_empresa) }}" target="_blank" class="w-full bg-[#4285F4] text-white py-4 rounded-2xl font-bold text-center flex items-center justify-center gap-2">
                        ⭐ Avaliar no Google Maps
                    </a>
                </div>
            `);
        }
    }

    startFlow();
</script>
@endsection
