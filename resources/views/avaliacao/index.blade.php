<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avalie - {{ $cliente->nome_empresa }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#7c3aed">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/icon-512.png">
    @vite(['resources/css/app.css'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        body.lang-jp { font-family: 'Noto Sans JP', sans-serif; letter-spacing: 0.02em; }
        .star { font-size: 48px; cursor: pointer; transition: transform 0.2s; color: #ddd; }
        .progress-bar { transition: width 0.4s ease; }
        .success-animation { animation: scaleIn 0.5s ease-out; }
        @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
        .star:hover { transform: scale(1.1); }
        .star.selected { color: #ffc107; }
    </style>
</head>
<body class="bg-gray-100 {{ $cliente->pais === 'jp' ? 'lang-jp' : '' }}">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="bg-purple-600 text-white p-6 text-center relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-purple-900/30">
                    <div id="progressBar" class="h-full bg-white progress-bar" style="width: 5%"></div>
                </div>
                <h1 class="text-2xl font-bold">{{ $cliente->nome_empresa }}</h1>
                <p class="opacity-90 mt-1">{{ $cliente->pais === 'jp' ? '本日の体験はいかがでしたでしょうか？' : 'Como foi sua experiência?' }}</p>
            </div>

            <div class="p-6 h-[400px] overflow-y-auto" id="chatMessages">
                <div class="flex justify-start mb-4">
                    <div class="bg-gray-100 rounded-2xl rounded-bl-none px-4 py-2 max-w-[80%]">
                        ${msgBoasVindas}
                    </div>
                </div>
            </div>

            <div class="p-6 border-t" id="inputArea">
                <div id="starsContainer" class="flex justify-center gap-2"></div>
                <div id="problemGrid" class="hidden"></div>
                <div id="contactArea" class="hidden mt-4"></div>
                <div id="loading" class="hidden text-center py-4">Enviando...</div>
            </div>
        </div>
    </div>

    <script>
        const clienteSlug = '{{ $cliente->slug }}';
        
        // Versões de Mensagens (BR e JP) do Banco de Dados
        const scripts = {
            br: {
                boasVindas: '{{ $cliente->msg_boas_vindas_br }}',
                perguntaNota: '{{ $cliente->msg_pergunta_nota_br }}',
                alta: '{{ $cliente->msg_agradecimento_alta_br }}',
                baixa: '{{ $cliente->msg_agradecimento_baixa_br }}'
            },
            jp: {
                boasVindas: '{{ $cliente->msg_boas_vindas_jp }}',
                perguntaNota: '{{ $cliente->msg_pergunta_nota_jp }}',
                alta: '{{ $cliente->msg_agradecimento_alta_jp }}',
                baixa: '{{ $cliente->msg_agradecimento_baixa_jp }}'
            }
        };

        const clientPais = '{{ $cliente->pais }}';
        
        // Auto-detecção de Idioma do Smartphone do CLIENTE
        const browserLang = navigator.language || 'pt-BR';
        const isJp = browserLang.startsWith('ja');
        const lang = isJp ? 'jp' : 'br';
        
        // Aplica a tradução selecionada
        const msgBoasVindas = scripts[lang].boasVindas;
        const msgPerguntaNota = scripts[lang].perguntaNota;
        const msgAlta = scripts[lang].alta;
        const msgBaixa = scripts[lang].baixa;
        
        if (isJp) document.body.classList.add('lang-jp');

        let step = 1;
        let totalSteps = 5;
        let notaSelecionada = 0;
        let problemaSelecionado = '';
        let tipoContato = null;
        let contatoValor = '';
        let primeiraVisita = null;
        let periodoVisita = null;
        let fotoData = null;

        function updateProgress(currentStep) {
            const pct = (currentStep / totalSteps) * 100;
            document.getElementById('progressBar').style.width = `${pct}%`;
        }

        function addMessage(text, isUser = false) {
            const messages = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = `flex ${isUser ? 'justify-end' : 'justify-start'} mb-4`;
            div.innerHTML = `<div class="${isUser ? 'bg-purple-600 text-white rounded-br-none' : 'bg-gray-100 rounded-bl-none'} rounded-2xl px-4 py-2 max-w-[80%]">${text}</div>`;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }

        function showTyping(callback) {
            const messages = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex justify-start mb-4';
            typingDiv.id = 'typing';
            typingDiv.innerHTML = '<div class="bg-gray-100 rounded-2xl rounded-bl-none px-4 py-2"><div class="flex gap-1"><span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></span><span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.2s"></span><span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.4s"></span></div></div>';
            messages.appendChild(typingDiv);
            messages.scrollTop = messages.scrollHeight;
            
            setTimeout(() => {
                const typing = document.getElementById('typing');
                if (typing) typing.remove();
                if (callback) callback();
            }, 900);
        }

        function showStars() {
            const container = document.getElementById('starsContainer');
            container.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.textContent = '★';
                star.onclick = () => onStarClick(i);
                container.appendChild(star);
            }
        }

        function onStarClick(nota) {
            notaSelecionada = nota;
            addMessage('⭐'.repeat(nota), true);
            document.getElementById('starsContainer').innerHTML = '';
            
            showTyping(() => {
                if (nota >= 4) {
                    addMessage(msgAlta);
                    setTimeout(() => finalizar(), 800);
                } else {
                    addMessage(msgBaixa);
                    showProblemGrid();
                }
            });
        }

        function showProblemGrid() {
            const problemas = ['Atendimento lento', 'Qualidade do produto', 'Limpeza/Ambiente', 'Preço não justo', 'Outro motivo'];
            const container = document.getElementById('problemGrid');
            container.innerHTML = '<div class="grid grid-cols-2 gap-2 mt-4"></div>';
            const grid = container.querySelector('div');
            
            problemas.forEach(prob => {
                const btn = document.createElement('button');
                btn.textContent = prob;
                btn.className = 'bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full text-sm';
                btn.onclick = () => {
                    problemaSelecionado = prob;
                    container.innerHTML = '';
                    addMessage(prob, true);
                    showTyping(() => {
                        const label = pais === 'jp' ? '改善点をお聞かせください（任意）:' : 'Conte um pouco mais sobre o que aconteceu (opcional):';
                        addMessage(label);
                        showInputFeedback();
                    });
                };
                grid.appendChild(btn);
            });
            container.classList.remove('hidden');
        }

        function showInputFeedback() {
            const container = document.getElementById('contactArea');
            const placeholder = pais === 'jp' ? '空白でも可...' : 'Sua opinião nos ajuda a melhorar...';
            const btnLabel = pais === 'jp' ? '送信' : 'Enviar';
            container.innerHTML = `
                <textarea id="feedbackText" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="${placeholder}"></textarea>
                <button onclick="sendFeedback()" class="w-full mt-3 bg-purple-600 text-white py-2 rounded-lg">${btnLabel}</button>
            `;
            container.classList.remove('hidden');
        }

        function sendFeedback() {
            const feedback = document.getElementById('feedbackText').value;
            if (feedback) addMessage(feedback, true);
            document.getElementById('contactArea').innerHTML = '';
            showTyping(() => {
                const label = isJp 
                    ? '管理者がこの問題を解決した際にお知らせできるよう、お名前とご連絡先をご入力いただけますか？（任意）' 
                    : 'Para que possamos te ouvir e te avisar assim que o problema for solucionado, deixe seu contato (é opcional):';
                addMessage(label);
                showOptionalContactInput();
            });
        }

        function showOptionalContactInput() {
            const container = document.getElementById('contactArea');
            container.innerHTML = `
                <div class="space-y-3 mt-4">
                    <input type="text" id="custName" placeholder="${isJp ? 'お名前' : 'Seu nome'}" class="w-full border rounded-lg px-3 py-3">
                    <input type="text" id="custPhone" placeholder="${isJp ? 'お電話番号 / LINE ID' : 'WhatsApp'}" class="w-full border rounded-lg px-3 py-3">
                    <button onclick="submitFinalFeedback()" class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold">
                        ${isJp ? '評価を送信する' : 'Enviar Avaliação'}
                    </button>
                    <p class="text-[10px] text-gray-400 text-center">
                        ${isJp ? '入力しない場合は匿名で送信されます' : 'Se preferir não preencher, sua avaliação será enviada anonimamente.'}
                    </p>
                </div>
            `;
            container.classList.remove('hidden');
        }

        function submitFinalFeedback() {
            const nome = document.getElementById('custName').value;
            const fone = document.getElementById('custPhone').value;
            
            if (nome && fone) {
                nomeCliente = nome;
                tipoContato = isJp ? 'line' : 'whatsapp';
                contatoValor = fone;
                addMessage(`${nome} - ${fone}`, true);
            } else {
                tipoContato = 'nao';
            }
            
            document.getElementById('contactArea').innerHTML = '';
            finalizar();
        }

        async function finalizar() {
            const loading = document.getElementById('loading');
            loading.classList.remove('hidden');
            
            const payload = {
                nota: notaSelecionada,
                feedback: document.getElementById('feedbackText')?.value || '',
                problema: problemaSelecionado,
                tipo_contato: tipoContato,
                contato_valor: contatoValor
            };

            try {
                const response = await fetch(`/avaliacao/${clienteSlug}/salvar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                loading.classList.add('hidden');
                
                if (result.success) {
                    processSuccess(notaSelecionada);
                }
            } catch (error) {
                // FALLBACK OFFLINE
                loading.classList.add('hidden');
                saveOffline(payload);
                processSuccess(notaSelecionada, true);
            }
        }

        function saveOffline(payload) {
            let pending = JSON.parse(localStorage.getItem('pending_reviews') || '[]');
            pending.push({
                ...payload,
                cliente_slug: clienteSlug,
                timestamp: new Date().getTime()
            });
            localStorage.setItem('pending_reviews', JSON.stringify(pending));
        }

        function processSuccess(nota, isOffline = false) {
            updateProgress(5);
            const inputArea = document.getElementById('inputArea');
            inputArea.innerHTML = '';
            
            showTyping(() => {
                const thanksMsg = isJp ? 'フィードバックをありがとうございました! 🙏' : 'Obrigado pelo feedback! 🙏';
                addMessage(thanksMsg);
                
                if (isOffline) {
                    const offlineMsg = isJp ? '現在オフラインです。接続が回復次第、送信されます。' : 'Você está offline. Sua avaliação foi salva e será enviada assim que a conexão voltar.';
                    addMessage(offlineMsg);
                }

                // CTA do Google Maps para TODOS (Compliance e Transparência)
                const ctaMsg = nota >= 4 
                    ? (isJp ? 'よろしければ、Google マップで体験を共有してください。' : 'Que tal contar para todos no Google Maps?')
                    : (isJp ? '私たちは透明性を大切にしています。ご希望であれば、Google マップに投稿することも可能です。' : 'Valorizamos a transparência. Se desejar, você também pode registrar sua opinião no Google Maps.');
                
                addMessage(ctaMsg);
                
                const ctaBtnLabel = isJp ? 'Google マップで表示' : 'Ver no Google Maps';
                
                // Exibe o botão do Google Maps
                const inputArea = document.getElementById('inputArea');
                inputArea.innerHTML = `
                    <div class="mt-4 animate-bounce">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($cliente->nome_empresa) }}" target="_blank" class="block w-full bg-white border-2 border-purple-600 text-purple-600 font-bold py-3 rounded-lg text-center">
                            🌐 ${ctaBtnLabel}
                        </a>
                    </div>
                `;

                setTimeout(() => {
                    const messages = document.getElementById('chatMessages');
                    messages.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full success-animation">
                            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold">${isJp ? '送信完了' : 'Enviado com sucesso!'}</h3>
                            <p class="text-gray-500 text-sm mt-1">${isJp ? 'この画面はまもなく閉じます' : 'Esta tela fechará em instantes'}</p>
                        </div>
                    `;
                    setTimeout(() => window.close(), 6000);
                }, 5000);
            });
        }

        // --- Novas Perguntas de Contexto ---
        function askPrimeiraVisita() {
            updateProgress(1);
            showTyping(() => {
                addMessage(isJp ? '今回が初めてのご来店ですか？' : 'Foi sua primeira visita?');
                const container = document.getElementById('inputArea');
                container.innerHTML = `
                    <div class="flex gap-2">
                        <button onclick="setPrimeiraVisita(true)" class="flex-1 bg-white border border-purple-600 text-purple-600 py-2 rounded-lg">${isJp ? 'はい、初めてです' : 'Sim, primeira vez'}</button>
                        <button onclick="setPrimeiraVisita(false)" class="flex-1 bg-white border border-purple-600 text-purple-600 py-2 rounded-lg">${isJp ? 'いいえ、何度か来ています' : 'Já sou cliente'}</button>
                    </div>
                `;
            });
        }

        function setPrimeiraVisita(val) {
            primeiraVisita = val;
            addMessage(val ? (isJp ? 'はい' : 'Sim') : (isJp ? 'いいえ' : 'Não'), true);
            document.getElementById('inputArea').innerHTML = '';
            askPeriodo();
        }

        function askPeriodo() {
            updateProgress(2);
            showTyping(() => {
                addMessage(isJp ? 'どの時間帯にご利用されましたか？' : 'Qual período você nos visitou?');
                const container = document.getElementById('inputArea');
                const periodos = isJp ? ['ランチ', 'ディナー', 'カフェ/その他'] : ['Almoço', 'Jantar', 'Outro'];
                container.innerHTML = `<div class="grid grid-cols-3 gap-2"></div>`;
                const grid = container.querySelector('div');
                periodos.forEach(p => {
                    const btn = document.createElement('button');
                    btn.className = 'bg-white border border-purple-600 text-purple-600 py-2 rounded-lg text-sm';
                    btn.textContent = p;
                    btn.onclick = () => {
                        periodoVisita = p;
                        addMessage(p, true);
                        container.innerHTML = '';
                        askRating();
                    };
                    grid.appendChild(btn);
                });
            });
        }

        function askRating() {
            updateProgress(3);
            showTyping(() => {
                addMessage(msgPerguntaNota);
                showStars();
            });
        }

        function showInputFeedback() {
            updateProgress(4);
            const container = document.getElementById('contactArea');
            const placeholder = isJp ? '空白でも可...' : 'Sua opinião nos ajuda a melhorar...';
            const btnLabel = isJp ? '送信' : 'Enviar';
            container.innerHTML = `
                <div class="flex flex-col gap-2">
                    <textarea id="feedbackText" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="${placeholder}"></textarea>
                    <div class="flex items-center justify-between">
                         <label class="cursor-pointer text-purple-600 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            ${isJp ? '写真を送る' : 'Foto (opcional)'}
                            <input type="file" accept="image/*" class="hidden" onchange="handleFoto(this)">
                        </label>
                        <span id="fotoStatus" class="text-xs text-green-600 hidden">✅ OK</span>
                    </div>
                    <button onclick="sendFeedback()" class="w-full mt-2 bg-purple-600 text-white py-2 rounded-lg">${btnLabel}</button>
                </div>
            `;
            container.classList.remove('hidden');
        }

        function handleFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    fotoData = e.target.result;
                    document.getElementById('fotoStatus').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function finalizar() {
            const loading = document.getElementById('loading');
            loading.classList.remove('hidden');
            
            const payload = {
                nota: notaSelecionada,
                feedback: document.getElementById('feedbackText')?.value || '',
                problema: problemaSelecionado,
                tipo_contato: tipoContato,
                contato_valor: contatoValor,
                primeira_visita: primeiraVisita,
                periodo_visita: periodoVisita,
                foto_problema: fotoData
            };

            try {
                const response = await fetch(`/avaliacao/${clienteSlug}/salvar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                loading.classList.add('hidden');
                
                if (result.success) {
                    processSuccess(notaSelecionada);
                }
            } catch (error) {
                // FALLBACK OFFLINE
                loading.classList.add('hidden');
                saveOffline(payload);
                processSuccess(notaSelecionada, true);
            }
        }

        function saveOffline(payload) {
            let pending = JSON.parse(localStorage.getItem('pending_reviews') || '[]');
            pending.push({
                ...payload,
                cliente_slug: clienteSlug,
                timestamp: new Date().getTime()
            });
            localStorage.setItem('pending_reviews', JSON.stringify(pending));
        }

        // Background Sync para avaliações pendentes
        setInterval(async () => {
            if (!navigator.onLine) return;
            
            let pending = JSON.parse(localStorage.getItem('pending_reviews') || '[]');
            if (pending.length === 0) return;

            const review = pending[0];
            try {
                const response = await fetch(`/avaliacao/${review.cliente_slug}/salvar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(review)
                });
                
                if (response.ok) {
                    pending.shift();
                    localStorage.setItem('pending_reviews', JSON.stringify(pending));
                    console.log('Avaliação offline enviada com sucesso!');
                }
            } catch (e) {
                console.log('Falha ao tentar sincronizar offline...');
            }
        }, 30000);

        // Start
        setTimeout(() => {
            askPrimeiraVisita();
        }, 500);
        // Register PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>
</html>
