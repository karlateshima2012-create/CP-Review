@extends('layouts.app')

@section('title', 'Avaliar Experiência')

@section('content')
<div class="min-h-screen bg-[#F0F2F5] flex flex-col max-w-lg mx-auto shadow-2xl overflow-hidden relative border-x border-gray-200">
    
    <!-- Header -->
    <div class="bg-white p-4 border-b flex items-center gap-3 z-10 shadow-sm">
        <div class="relative">
            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white text-xl">
                <span>🤖</span>
            </div>
            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
        </div>
        <div>
            <h1 class="font-bold text-gray-800 text-lg leading-tight">{{ $cliente->nome_empresa }}</h1>
            <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Atendimento Digital Ativo</p>
        </div>
    </div>

    <!-- Chat Area -->
    <div id="chat-container" class="flex-1 p-4 space-y-4 overflow-y-auto pb-32">
        <!-- Messages will be injected here -->
    </div>

    <!-- Typing Indicator -->
    <div id="typing-indicator" class="hidden absolute bottom-28 left-4 z-10">
        <div class="bg-white p-3 rounded-2xl rounded-bl-none shadow-sm flex gap-1">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
        </div>
    </div>

    <!-- Floating Input Area -->
    <div id="input-area" class="absolute bottom-0 left-0 right-0 bg-white p-4 border-t shadow-lg transition-transform duration-300 transform translate-y-full z-20">
        <!-- Dynamic controls (stars, options, text) -->
    </div>
</div>

<style>
    .chat-bubble {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 20px;
        font-size: 15px;
        line-height: 1.4;
        position: relative;
        animation: bubblePop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    @keyframes bubblePop {
        from { opacity: 0; transform: scale(0.8) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    #chat-container::-webkit-scrollbar { display: none; }
</style>

<script>
    const chatContainer = document.getElementById('chat-container');
    const typingIndicator = document.getElementById('typing-indicator');
    const inputArea = document.getElementById('input-area');

    let state = {
        nota: 0,
        problema: null,
        feedback: ''
    };

    const strings = {
        br: {
            welcome: "Olá!👋 Prazer em falar com você. Sou o assistente de qualidade da <b>{{ $cliente->nome_empresa }}</b>.",
            askRating: "Em uma escala de 1 a 5, como você avalia sua experiência conosco hoje?✨",
            empathy: "Lamento muito que sua experiência não tenha sido 100% positiva.😔 Queremos muito entender o que aconteceu para melhorar.",
            enthusiasm: "Que alegria!🤩 Saber que você gostou do nosso trabalho é o que nos motiva. Pode nos contar o que mais te agradou?",
            askProblem: "Qual destes pontos mais te incomodou?👇",
            askPositive: "Qual destes pontos você mais gostou?👇",
            askComment: "Se quiser, deixe um comentário adicional sobre sua experiência:",
            thanks: "Recebemos sua avaliação! Muito obrigado pela ajuda em nosso crescimento.🚀"
        },
        jp: {
            welcome: "こんにちは！👋 <b>{{ $cliente->nome_empresa }}</b> のカスタマーサポートアシスタントです。",
            askRating: "本日の体験はいかがでしたでしょうか？ 5段階（1:不満〜5:満足）でお聞かせください。✨",
            empathy: "ご期待に沿えず申し訳ございません。😔 今後の改善のため、詳しくお聞かせいただけますでしょうか？",
            enthusiasm: "嬉しいお言葉をありがとうございます！🤩 特にどの点にご満足いただけましたか？",
            askProblem: "気になった点はどれでしょうか？👇",
            askPositive: "良かった点はどれでしょうか？👇",
            askComment: "その他、お気づきの点がございましたらお聞かせください：",
            thanks: "貴重なご意見ありがとうございました。🚀 またのご来店を心よりお待ちしております。"
        }
    };

    const lang = (navigator.language.startsWith('ja')) ? 'jp' : 'br';
    const s = strings[lang];

    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    function addBubble(text, type = 'bot') {
        const wrapper = document.createElement('div');
        wrapper.className = `flex flex-col ${type === 'bot' ? 'items-start' : 'items-end'}`;
        
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${type === 'bot' ? 'bubble-bot' : 'bubble-user'}`;
        bubble.innerHTML = text;
        
        wrapper.appendChild(bubble);
        chatContainer.appendChild(wrapper);
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
    }

    async function showTyping(ms = 800) {
        typingIndicator.classList.remove('hidden');
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        await sleep(ms);
        typingIndicator.classList.add('hidden');
    }

    function toggleInput(show, html = '') {
        if (show) {
            inputArea.innerHTML = html;
            inputArea.classList.remove('translate-y-full');
        } else {
            inputArea.classList.add('translate-y-full');
        }
    }

    async function startFlow() {
        await sleep(1000);
        await showTyping(1200);
        addBubble(s.welcome);
        
        await sleep(500);
        await showTyping(1000);
        addBubble(s.askRating);

        toggleInput(true, `
            <div class="flex justify-between gap-2 max-w-sm mx-auto">
                ${[1,2,3,4,5].map(i => `
                    <button onclick="handleRating(${i})" class="flex-1 p-2 text-3xl transition-transform active:scale-90">
                        ${i <= 3 ? '⭐' : '🌟'}
                        <div class="text-[10px] font-bold text-gray-400 mt-1">${i}</div>
                    </button>
                `).join('')}
            </div>
        `);
    }

    window.handleRating = async (rating) => {
        state.nota = rating;
        toggleInput(false);
        addBubble(`${rating} ${rating === 1 ? 'Estrela' : 'Estrelas'}`, 'user');

        await sleep(800);
        await showTyping(1000);

        if (rating <= 3) {
            addBubble(s.empathy);
            await sleep(500);
            addBubble(s.askProblem);
            const options = ['Atendimento 👤', 'Demora ⏰', 'Qualidade 🍽️', 'Limpeza 🧼'];
            toggleInput(true, `
                <div class="grid grid-cols-2 gap-2">
                    ${options.map(opt => `
                        <button onclick="handleOption('${opt}')" class="p-3 border-2 border-purple-50 rounded-xl text-sm font-semibold hover:bg-purple-100 transition-colors">
                            ${opt}
                        </button>
                    `).join('')}
                </div>
            `);
        } else {
            addBubble(s.enthusiasm);
            await sleep(500);
            addBubble(s.askPositive);
            const options = ['Sabor 😋', 'Equipe 🤝', 'Rapidez ⚡', 'Ambiente ✨'];
            toggleInput(true, `
                <div class="grid grid-cols-2 gap-2">
                    ${options.map(opt => `
                        <button onclick="handleOption('${opt}')" class="p-3 border-2 border-purple-50 rounded-xl text-sm font-semibold hover:bg-purple-100 transition-colors">
                            ${opt}
                        </button>
                    `).join('')}
                </div>
            `);
        }
    };

    window.handleOption = async (option) => {
        state.problema = option;
        toggleInput(false);
        addBubble(option, 'user');

        await sleep(600);
        await showTyping(800);
        addBubble(s.askComment);

        toggleInput(true, `
            <div class="space-y-3">
                <textarea id="feedback-txt" class="w-full p-4 border rounded-2xl text-sm focus:ring-purple-600 focus:border-purple-600 outline-none" rows="3" placeholder="Quer nos contar mais detalhes?"></textarea>
                <button onclick="handleFinal()" class="w-full bg-purple-600 text-white p-4 rounded-2xl font-bold shadow-lg transition-transform active:scale-95">
                    Finalizar e Enviar
                </button>
            </div>
        `);
    };

    window.handleFinal = async () => {
        state.feedback = document.getElementById('feedback-txt').value;
        toggleInput(false);
        
        if (state.feedback) addBubble(state.feedback, 'user');

        await showTyping(1500);
        addBubble(s.thanks);

        // Server sync
        fetch("{{ route('avaliacao.salvar', $cliente->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(state)
        });

        if (state.nota >= 4) {
            await sleep(800);
            await showTyping(1000);
            addBubble("Pode compartilhar essa alegria no Google também? Isso nos ajuda demais!👇");
            toggleInput(true, `
                <a href="https://search.google.com/local/writereview?placeid=SEU_PLACE_ID_AQUI" target="_blank" class="w-full bg-[#4285F4] text-white p-4 rounded-2xl font-bold text-center block shadow-md">
                    ⭐ Avaliar no Google Maps
                </a>
            `);
        }
    };

    startFlow();
</script>
@endsection
