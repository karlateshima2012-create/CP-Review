@extends('layouts.app')

@section('title', 'Avaliar Experiência')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

:root {
  --bg: #0F0F0F;
  --surface: #1A1A1A;
  --surface2: #242424;
  --border: #2E2E2E;
  --bubble-bot: #1E1E1E;
  --bubble-bot-border: #2A2A2A;
  --bubble-user: #2563EB;
  --bubble-user-dark: #1D4ED8;
  --text: #F0F0F0;
  --text-muted: #888;
  --text-dim: #555;
  --accent: #2563EB;
  --accent-glow: rgba(37,99,235,0.25);
  --green: #10B981;
  --green-glow: rgba(16,185,129,0.2);
  --yellow: #F59E0B;
  --red: #EF4444;
  --radius-bubble: 20px;
  --radius-btn: 14px;
  --font: 'Nunito', sans-serif;
}

body {
  background: #080808;
  font-family: var(--font);
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.bot-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #080808;
}

.phone {
  width: 100%;
  max-width: 430px;
  height: 100vh;
  background: var(--bg);
  position: relative;
  display: flex;
  flex-direction: column;
  box-shadow: 0 0 100px rgba(0,0,0,0.5);
}

@media (min-width: 500px) {
    .phone {
        height: 844px;
        border-radius: 40px;
        border: 4px solid #1A1A1A;
    }
}

/* HEADER */
.header {
  flex-shrink: 0;
  padding: 40px 20px 14px;
  background: rgba(15,15,15,.92);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 10;
}

.biz-avatar {
  width: 40px; height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, #2563EB, #7C3AED);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}

.biz-name { font-size: 15px; font-weight: 700; color: var(--text); }
.biz-status { font-size: 11px; color: var(--green); display: flex; align-items: center; gap: 5px; }
.biz-status::before { content:''; width:6px; height:6px; background:var(--green); border-radius:50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

/* CHAT */
.chat-area {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.chat-area::-webkit-scrollbar { display: none; }

.msg { display: flex; animation: msgIn 0.3s ease-out both; }
.msg.bot { justify-content: flex-start; }
.msg.user { justify-content: flex-end; }

@keyframes msgIn { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }

.bubble {
  max-width: 80%;
  padding: 12px 16px;
  font-size: 14px;
  line-height: 1.5;
  font-weight: 500;
}

.bot-bubble {
  background: var(--bubble-bot);
  border: 1px solid var(--bubble-bot-border);
  border-radius: 18px 18px 18px 4px;
  color: var(--text);
}

.user-bubble {
  background: var(--bubble-user);
  border-radius: 18px 18px 4px 18px;
  color: white;
  font-weight: 600;
}

/* TYPING */
.typing { display: flex; gap: 4px; padding: 5px 0; }
.typing span { width: 6px; height: 6px; background: #555; border-radius: 50%; animation: typeDot 1.2s infinite; }
.typing span:nth-child(2) { animation-delay: 0.2s; }
.typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typeDot { 0%,60%,100%{opacity:.3;transform:scale(1)} 30%{opacity:1;transform:scale(1.2)} }

/* STAR BTN */
.star-container { display: flex; gap: 8px; margin-top: 5px; }
.star-btn {
  width: 44px; height: 44px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 12px;
  font-size: 20px;
  cursor: pointer;
  transition: 0.2s;
}
.star-btn:active { transform: scale(0.9); }
.star-btn.active { border-color: var(--yellow); background: rgba(245,158,11,0.1); }

/* WIDGETS */
.qr-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px; }
.qr-btn {
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--text);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.input-box {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 12px;
  color: white;
  width: 100%;
  font-family: inherit;
  margin-top: 8px;
  outline: none;
}
.send-btn {
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 10px 20px;
  margin-top: 8px;
  font-weight: 700;
  width: 100%;
}

.bottom-bar { padding: 15px; text-align: center; font-size: 10px; color: var(--text-dim); border-top: 1px solid var(--border); }
</style>

<div class="bot-container">
    <div class="phone">
        <!-- Header -->
        <div class="header">
            <div class="biz-avatar">💼</div>
            <div class="biz-info">
                <div class="biz-name">{{ $cliente->nome_empresa }}</div>
                <div class="biz-status">Online agora</div>
            </div>
            <div style="margin-left: auto; font-size: 10px; color: var(--green); border: 1px solid var(--green); padding: 2px 6px; border-radius: 5px;">
                🔒 PRIVADO
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area" id="chat"></div>

        <!-- Bottom Bar -->
        <div class="bottom-bar">
            🛡️ Seus dados estão seguros e são usados apenas para melhoria interna.
        </div>
    </div>
</div>

<script>
const chat = document.getElementById('chat');
let state = { nota: 0, motivo: '', feedback: '' };

function scroll() { chat.scrollTo({ top: chat.scrollHeight, behavior: 'smooth' }); }

async function addTyping() {
    const div = document.createElement('div');
    div.id = 'typing';
    div.className = 'msg bot';
    div.innerHTML = `<div class="bubble bot-bubble"><div class="typing"><span></span><span></span><span></span></div></div>`;
    chat.appendChild(div);
    scroll();
    await new Promise(r => setTimeout(r, 1200));
    div.remove();
}

async function addBotMsg(text) {
    await addTyping();
    const div = document.createElement('div');
    div.className = 'msg bot';
    div.innerHTML = `<div class="bubble bot-bubble">${text}</div>`;
    chat.appendChild(div);
    scroll();
}

function addUserMsg(text) {
    const div = document.createElement('div');
    div.className = 'msg user';
    div.innerHTML = `<div class="bubble user-bubble">${text}</div>`;
    chat.appendChild(div);
    scroll();
}

async function start() {
    await addBotMsg("Olá! 👋 Bem-vindo ao suporte de qualidade da <b>{{ $cliente->nome_empresa }}</b>.");
    await addBotMsg("Como foi sua experiência conosco hoje? Sua nota ajuda muito nosso time! ✨");
    
    const stars = document.createElement('div');
    stars.className = 'star-container';
    stars.innerHTML = [1,2,3,4,5].map(i => `<button class="star-btn" onclick="setRating(${i})">⭐</button>`).join('');
    chat.appendChild(stars);
    scroll();
}

window.setRating = async (n) => {
    state.nota = n;
    document.querySelector('.star-container').remove();
    addUserMsg(`${n} estrelas`);

    if (n <= 3) {
        await addBotMsg("Poxa, lamento que não tenha sido perfeito. 😔");
        await addBotMsg("O que mais te incomodou hoje?");
        const options = ['Atendimento 👤', 'Demora ⏰', 'Qualidade 🍽️', 'Limpeza 🧼'];
        renderOptions(options);
    } else {
        await addBotMsg("Que notícia maravilhosa! 🤩");
        await addBotMsg("O que você mais gostou na visita?");
        const options = ['Sabor 😋', 'Equipe 🤝', 'Velocidade ⚡', 'Ambiente ✨'];
        renderOptions(options);
    }
};

function renderOptions(options) {
    const wrap = document.createElement('div');
    wrap.className = 'qr-wrap';
    wrap.innerHTML = options.map(opt => `<button class="qr-btn" onclick="setOption('${opt}')">${opt}</button>`).join('');
    chat.appendChild(wrap);
    scroll();
}

window.setOption = async (opt) => {
    state.motivo = opt;
    document.querySelector('.qr-wrap').remove();
    addUserMsg(opt);

    await addBotMsg("Obrigado por nos contar! 🙏");
    await addBotMsg("Quer deixar algum detalhe adicional ou sugestão?");
    
    const inputWrap = document.createElement('div');
    inputWrap.innerHTML = `
        <textarea id="f-area" class="input-box" placeholder="Escreva aqui..."></textarea>
        <button class="send-btn" onclick="finish()">Enviar Avaliação</button>
    `;
    chat.appendChild(inputWrap);
    scroll();
};

window.finish = async () => {
    state.feedback = document.getElementById('f-area').value;
    document.getElementById('f-area').parentElement.remove();
    if(state.feedback) addUserMsg(state.feedback);

    await addBotMsg("Recebido! 🚀");
    await addBotMsg("Sua avaliação foi enviada diretamente para a nossa gerência.");
    
    // Server push
    fetch("{{ route('avaliacao.salvar', $cliente->slug) }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            nota: state.nota,
            problema: state.motivo,
            feedback: state.feedback
        })
    });

    if (state.nota >= 4) {
        await addBotMsg("Como você teve uma ótima experiência, poderia nos ajudar avaliando no Google também? ✨");
        const gBtn = document.createElement('div');
        gBtn.innerHTML = `
            <a href="https://www.google.com/maps?q={{ urlencode($cliente->nome_empresa) }}" target="_blank" 
               style="display:block; background:white; color:#4285F4; text-align:center; padding:15px; border-radius:15px; font-weight:bold; text-decoration:none; margin-top:10px; border:1px solid #ddd;">
               🚀 Abrir Google Reviews
            </a>
        `;
        chat.appendChild(gBtn);
    } else {
        await addBotMsg("Trabalharemos duro para que sua próxima visita seja 5 estrelas! 🍜");
    }
    
    scroll();
};

start();
</script>
@endsection
