@extends('layouts.app')

@section('title', 'Como foi sua visita?')

@section('content')
<script src="/pwa/offline-queue.js"></script>
<script src="/pwa/photo-upload.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700;800&display=swap');

:root {
  --bg: #F3F4F6;
  --surface: #FFFFFF;
  --surface2: #F9FAFB;
  --border: #E5E7EB;
  --bubble-bot: #FFFFFF;
  --bubble-bot-border: #E5E7EB;
  --bubble-user: #7C3AED;
  --bubble-user-dark: #6D28D9;
  --text: #111827;
  --text-muted: #4B5563;
  --text-dim: #9CA3AF;
  --accent: #7C3AED;
  --accent-glow: rgba(124, 58, 237, 0.15);
  --header-bg: #7C3AED;
  --green: #10B981;
  --yellow: #F59E0B;
  --red: #EF4444;
  --radius-bubble: 20px;
  --radius-btn: 14px;
  --font: 'IBM Plex Sans', sans-serif;
}

body {
  background: #E5E7EB;
  font-family: var(--font);
  margin: 0; padding: 0;
  overflow: hidden;
  overscroll-behavior: none;
}

.bot-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #E5E7EB;
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
  overflow: hidden;
}

@media (min-width: 500px) {
    .phone {
        height: 844px;
        border-radius: 40px;
        border: 12px solid #FFFFFF;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
    }
}

/* HEADER */
.header {
  flex-shrink: 0;
  padding: 45px 20px 15px;
  background: var(--header-bg);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 20;
  color: #FFFFFF;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.biz-avatar {
  width: 42px; height: 42px;
  border-radius: 12px;
  background: #FFFFFF;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.biz-info { flex: 1; }
.biz-name { font-size: 16px; font-weight: 700; color: #FFFFFF; line-height: 1.2; }
.biz-status { font-size: 12px; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 5px; font-weight: 500; }
.biz-status::before { content:''; width:6px; height:6px; background:#10B981; border-radius:50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }

/* CHAT AREA */
.chat-area {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  scroll-behavior: smooth;
  padding-bottom: 220px;
}
.chat-area::-webkit-scrollbar { display: none; }

.msg { display: flex; opacity: 0; transform: translateY(10px); animation: msgIn 0.3s forwards; }
.msg.bot { justify-content: flex-start; }
.msg.user { justify-content: flex-end; }
@keyframes msgIn { to { opacity:1; transform: translateY(0); } }

.bubble {
  max-width: 85%;
  padding: 14px 18px;
  font-size: 15px;
  line-height: 1.5;
  font-weight: 500;
}

.bot-bubble {
  background: var(--bubble-bot);
  border: 1px solid var(--bubble-bot-border);
  border-radius: 0 18px 18px 18px;
  color: var(--text);
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.user-bubble {
  background: var(--bubble-user);
  border-radius: 18px 0 18px 18px;
  color: white;
  font-weight: 600;
  box-shadow: 0 2px 4px var(--accent-glow);
}

/* TYPING */
.typing { display: flex; gap: 4px; padding: 6px 0; }
.typing span { width: 6px; height: 6px; background: var(--text-muted); border-radius: 50%; animation: typeDot 1.2s infinite; }
.typing span:nth-child(2) { animation-delay: 0.2s; }
.typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typeDot { 0%,60%,100%{opacity:.3;transform:scale(1)} 30%{opacity:1;transform:scale(1.2)} }

/* STAR RATING */
.star-rating {
    display: flex;
    justify-content: space-around;
    padding: 20px 0;
    width: 100%;
    background: var(--surface2);
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-top: 10px;
}
.star-item {
    font-size: 42px;
    cursor: pointer;
    transition: transform 0.2s;
    filter: grayscale(1) opacity(0.2);
}
.star-item.active {
    filter: grayscale(0) opacity(1);
    transform: scale(1.3);
}

/* QUICK REPLIES / BUTTONS */
.options-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 5px;
    width: 100%;
}
.qr-btn {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--accent);
    padding: 14px;
    border-radius: 16px;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}
.qr-btn:active { background: var(--surface2); transform: scale(0.98); }

.qr-row {
    display: flex;
    gap: 8px;
    margin-top: 5px;
}
.qr-row .qr-btn {
    flex: 1;
    justify-content: center;
}

/* GRID OF PROBLEMS */
.problems-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 5px;
}

/* INPUTS */
.input-container {
    background: var(--surface);
    border-top: 1px solid var(--border);
    padding: 15px;
    position: absolute;
    bottom: 0; width: 100%;
    z-index: 30;
    box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
}
.f-input {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 14px 16px;
    color: var(--text);
    width: 100%;
    font-family: inherit;
    outline: none;
    font-size: 15px;
    resize: none;
}
.f-input::placeholder { color: var(--text-dim); }
.confirm-btn {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 14px;
    margin-top: 10px;
    font-weight: 800;
    width: 100%;
    font-size: 15px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}

/* SUCCESS STATE */
.success-screen {
    position: absolute;
    inset: 0;
    background: var(--bg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 100;
    text-align: center;
    padding: 40px;
}
.success-icon {
    width: 80px; height: 80px;
    background: var(--green);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 40px;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
    color: #FFF;
}

/* PHOTO UPLOAD */
.photo-upload {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.photo-preview {
    width: 100%;
    height: 150px;
    border-radius: 12px;
    object-fit: cover;
    display: none;
    border: 2px solid var(--border);
}
.upload-label {
    background: var(--surface2);
    border: 1px dashed var(--border);
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    font-size: 12px;
    color: var(--text-muted);
    cursor: pointer;
}
</style>

<div class="bot-container">
    <div class="phone">
        <div class="header">
            <div class="biz-avatar">🏢</div>
            <div class="biz-info">
                <div class="biz-name" id="header-biz-name">...</div>
                <div class="biz-status">Online</div>
            </div>
            <div style="font-size: 11px; background: rgba(255,255,255,0.2); color: #FFF; padding: 4px 8px; border-radius: 8px; font-weight: 600;">
                PWA
            </div>
        </div>

        <div class="chat-area" id="chat"></div>

        <div id="footer-note" style="padding: 15px; text-align: center; font-size: 10px; color: var(--text-dim);">
            🛡️ Seus dados estão protegidos.
        </div>
    </div>
</div>

<script>
let botConfig = null;
let state = {
    nome_cliente: '',
    first_visit: null,
    period: null,
    rating: 0,
    aspect: null,
    problem: null,
    feedback: '',
    photo: null,
    contact: ''
};

const chat = document.getElementById('chat');
const bizSlug = "{{ $cliente->slug }}";

function scrollChat() {
    chat.scrollTo({ top: chat.scrollHeight, behavior: 'smooth' });
}

async function wait(ms) { return new Promise(r => setTimeout(r, ms)); }

async function addTyping() {
    const div = document.createElement('div');
    div.id = 'typing';
    div.className = 'msg bot';
    div.innerHTML = `<div class="bubble bot-bubble"><div class="typing"><span></span><span></span><span></span></div></div>`;
    chat.appendChild(div);
    scrollChat();
    await wait(1000);
    div.remove();
}

async function addBotMsg(text) {
    await addTyping();
    const div = document.createElement('div');
    div.className = 'msg bot';
    div.innerHTML = `<div class="bubble bot-bubble">${text}</div>`;
    chat.appendChild(div);
    scrollChat();
}

function addUserMsg(text) {
    const div = document.createElement('div');
    div.className = 'msg user';
    div.innerHTML = `<div class="bubble user-bubble">${text}</div>`;
    chat.appendChild(div);
    scrollChat();
}

async function init() {
    try {
        const locale = navigator.language.split('-')[0];
        const res = await fetch(`/api/bot-script/${bizSlug}?locale=${locale}`);
        botConfig = await res.json();
        document.getElementById('header-biz-name').innerText = botConfig.tenant.name;
        
        await startConversation();
    } catch (e) {
        console.error(e);
        await addBotMsg("Erro ao carregar o chat.");
    }
}

async function startConversation() {
    await addBotMsg(botConfig.lang.welcome);
    await wait(400);
    await askRating();
}

async function askRating() {
    const lines = botConfig.lang.askRate.split('\n');
    for (const line of lines) {
        await addBotMsg(line);
        await wait(400);
    }
    const div = document.createElement('div');
    div.className = 'star-rating';
    div.innerHTML = [1,2,3,4,5].map(i => `<span class="star-item" onclick="handleRating(${i})">⭐</span>`).join('');
    chat.appendChild(div);
    scrollChat();
}

window.handleRating = async (r) => {
    state.rating = r;
    event.target.closest('.star-rating').remove();
    addUserMsg("⭐".repeat(r));

    if (r >= 4) {
        await handlePositiveFlow();
    } else {
        await handleNegativeFlow();
    }
};

// ==========================================
// FLUXO POSITIVO 
// ==========================================
async function handlePositiveFlow() {
    await addBotMsg(botConfig.lang.highRate);
    await wait(300);
    await askFirstVisit(true);
}

// ==========================================
// FLUXO NEGATIVO 
// ==========================================
async function handleNegativeFlow() {
    await addBotMsg(botConfig.lang.lowRate);
    await wait(300);
    await addBotMsg(botConfig.lang.lowRateQ);

    const div = document.createElement('div');
    div.className = 'problems-grid';
    div.innerHTML = botConfig.lang.optionsLow.map(opt => `
        <button class="qr-btn" style="justify-content:center" onclick="handleProblem('${opt}')">${opt}</button>
    `).join('');
    chat.appendChild(div);
    scrollChat();
}

window.handleProblem = async (opt) => {
    state.problem = opt;
    event.target.closest('.problems-grid').remove();
    addUserMsg(opt);
    await addBotMsg("Entendi...");
    await wait(300);
    await askFeedbackText();
};

async function askFeedbackText() {
    await addBotMsg(botConfig.lang.q_optional_text);
    const div = document.createElement('div');
    div.className = 'input-container';
    div.innerHTML = `
        <textarea id="feedback-area" class="f-input" rows="2" placeholder="Sua opinião..."></textarea>
        <div style="display:flex; gap:10px; margin-top:10px">
            <button class="confirm-btn" style="background:var(--surface2); color:var(--text)" onclick="handleFeedbackSubmit(false)">${botConfig.lang.btn_feedback_no}</button>
            <button class="confirm-btn" onclick="handleFeedbackSubmit(true)">${botConfig.lang.btn_feedback_send}</button>
        </div>
    `;
    document.querySelector('.phone').appendChild(div);
    scrollChat();
}

window.handleFeedbackSubmit = async (hasValue) => {
    if (hasValue) {
        state.feedback = document.getElementById('feedback-area').value;
        addUserMsg(state.feedback || "Enviado");
    } else {
        addUserMsg(botConfig.lang.btn_skip);
    }
    document.querySelector('.input-container').remove();
    await wait(300);
    await askPhoto();
};

async function askPhoto() {
    await addBotMsg(botConfig.lang.q_optional_photo);
    const div = document.createElement('div');
    div.className = 'input-container';
    div.innerHTML = `
        <div class="photo-upload">
            <input type="file" id="photo-input" hidden accept="image/*" onchange="previewPhoto(this)">
            <label for="photo-input" class="upload-label">📸 Adicionar Foto</label>
            <img id="photo-preview" class="photo-preview">
        </div>
        <div style="display:flex; gap:10px; margin-top:10px">
            <button class="confirm-btn" style="background:var(--surface2); color:var(--text)" onclick="handlePhotoSubmit(false)">${botConfig.lang.btn_feedback_no}</button>
            <button class="confirm-btn" onclick="handlePhotoSubmit(true)">${botConfig.lang.btn_feedback_send}</button>
        </div>
    `;
    document.querySelector('.phone').appendChild(div);
    scrollChat();
}

window.previewPhoto = (input) => {
    if (input.files && input.files[0]) {
        state.photo = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('photo-preview');
            img.src = e.target.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

window.handlePhotoSubmit = async (hasValue) => {
    if (hasValue && state.photo) {
        addUserMsg(botConfig.lang.btn_send);
    } else {
        state.photo = null;
        addUserMsg(botConfig.lang.btn_skip);
    }
    document.querySelector('.input-container').remove();
    await wait(300);
    await askFirstVisit(false);
};

// ==========================================
// PERGUNTAS COMUNS (DIRECIONADAS)
// ==========================================
async function askFirstVisit(isPos) {
    await addBotMsg(botConfig.lang.q_first_visit);
    const div = document.createElement('div');
    div.className = 'qr-row';
    div.innerHTML = `
        <button class="qr-btn" onclick="handleFirstVisit(true, ${isPos})">${botConfig.lang.btn_yes}</button>
        <button class="qr-btn" onclick="handleFirstVisit(false, ${isPos})">${botConfig.lang.btn_no}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handleFirstVisit = async (val, isPos) => {
    state.first_visit = val;
    event.target.closest('.qr-row').remove();
    addUserMsg(val ? botConfig.lang.btn_yes : botConfig.lang.btn_no);
    await wait(300);
    await addBotMsg(isPos ? botConfig.lang.first_visit_ack : botConfig.lang.first_visit_ack_low);
    await wait(300);
    await askPeriod(isPos);
};

async function askPeriod(isPos) {
    await addBotMsg(botConfig.lang.q_period);
    const div = document.createElement('div');
    div.className = 'options-grid';
    div.innerHTML = `
        <button class="qr-btn" onclick="handlePeriod('lunch', ${isPos})">${botConfig.lang.btn_morning}</button>
        <button class="qr-btn" onclick="handlePeriod('afternoon', ${isPos})">${botConfig.lang.btn_afternoon}</button>
        <button class="qr-btn" onclick="handlePeriod('dinner', ${isPos})">${botConfig.lang.btn_night}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handlePeriod = async (p, isPos) => {
    state.period = p;
    event.target.closest('.options-grid').remove();
    const labels = { lunch: botConfig.lang.btn_morning, afternoon: botConfig.lang.btn_afternoon, dinner: botConfig.lang.btn_night };
    addUserMsg(labels[p]);
    await wait(300);
    
    if (isPos) {
        await addBotMsg(botConfig.lang.period_ack);
        await askRecommend();
    } else {
        await askContact();
    }
};

// ==========================================
// FINALIZAÇÃO POSITIVA
// ==========================================
async function askRecommend() {
    await addBotMsg(botConfig.lang.q_recommend);
    const div = document.createElement('div');
    div.className = 'options-grid';
    div.innerHTML = `
        <button class="qr-btn" onclick="handleRecommend('yes')">${botConfig.lang.btn_rec_yes}</button>
        <button class="qr-btn" onclick="handleRecommend('maybe')">${botConfig.lang.btn_rec_maybe}</button>
        <button class="qr-btn" onclick="handleRecommend('no')">${botConfig.lang.btn_rec_no}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handleRecommend = async (rec) => {
    event.target.closest('.options-grid').remove();
    const map = { yes: botConfig.lang.btn_rec_yes, maybe: botConfig.lang.btn_rec_maybe, no: botConfig.lang.btn_rec_no };
    addUserMsg(map[rec]);
    await wait(400);

    let msg = botConfig.lang.recommend_yes;
    if (rec === 'maybe') msg = botConfig.lang.recommend_maybe;
    if (rec === 'no') msg = botConfig.lang.recommend_no;

    const parts = msg.split('\n');
    for (const part of parts) {
        await addBotMsg(part);
        await wait(500);
    }
    
    await finishChat(true);
};

// ==========================================
// FINALIZAÇÃO NEGATIVA
// ==========================================
async function askContact() {
    await addBotMsg(botConfig.lang.q_contact);
    const div = document.createElement('div');
    div.className = 'options-grid';
    div.innerHTML = `
        <button class="qr-btn" onclick="handleContactChoice('whatsapp')">${botConfig.lang.btn_contact_wa}</button>
        <button class="qr-btn" onclick="handleContactChoice('line')">${botConfig.lang.btn_contact_line}</button>
        <button class="qr-btn" onclick="handleContactChoice('no')">${botConfig.lang.btn_contact_no}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handleContactChoice = async (c) => {
    event.target.closest('.options-grid').remove();
    const map = { whatsapp: botConfig.lang.btn_contact_wa, line: botConfig.lang.btn_contact_line, no: botConfig.lang.btn_contact_no };
    addUserMsg(map[c]);
    
    if (c === 'no') {
        state.contact = '';
        await finishChat(false);
    } else {
        const div = document.createElement('div');
        div.className = 'input-container';
        div.innerHTML = `
            <input type="text" id="contact-val" class="f-input" placeholder="Seu número/ID...">
            <button class="confirm-btn" onclick="submitContact('${c}')">Continuar ➔</button>
        `;
        document.querySelector('.phone').appendChild(div);
        scrollChat();
    }
};

window.submitContact = async (c) => {
    state.contact = document.getElementById('contact-val').value;
    document.querySelector('.input-container').remove();
    addUserMsg(state.contact || 'Ok');
    await wait(300);
    await finishChat(false);
};

// ==========================================
// GOOGLE & FINISH
// ==========================================
async function showGoogleBtn(msg, isPos) {
    if (msg) {
        const parts = msg.split('\n');
        for (const part of parts) {
            await addBotMsg(part);
            await wait(500);
        }
    }
    const gDiv = document.createElement('div');
    gDiv.style.padding = '5px 0';
    gDiv.innerHTML = `
        <a href="${botConfig.tenant.google_link}" target="_blank" onclick="handleGoogleClick()" class="confirm-btn" style="background:#4285F4; text-decoration:none">
            ${botConfig.lang.googleBtn}
        </a>
    `;
    chat.appendChild(gDiv);
    scrollChat();
}

window.handleGoogleClick = () => {
    setTimeout(() => {
        showSuccessScreen();
    }, 1000);
};

let isSubmitting = false;
async function finishChat(isPos) {
    if(isSubmitting) return;
    isSubmitting = true;

    // Send payload quietly in background
    submitEvaluation().catch(e => console.error(e));

    const finalTexts = (isPos ? botConfig.lang.highFinalMsg : botConfig.lang.lowFinalMsg).split('\n');
    for (const text of finalTexts) {
        await addBotMsg(text);
        await wait(600);
    }
    
    // IF positive (4-5 stars), we show Google Button as the VERY last thing
    if (isPos) {
        await showGoogleBtn(null, true);
    } else {
        setTimeout(() => {
            showSuccessScreen();
        }, 3000);
    }
}

async function submitEvaluation() {
    const payload = {
        nota: state.rating,
        feedback: state.feedback,
        problema: state.problem || state.aspect,
        tipo_contato: state.contact ? 'whatsapp' : 'nao', // Simplificado
        contato_valor: state.contact,
        nome_cliente: state.nome_cliente || 'Anônimo',
        primeira_visita: state.first_visit,
        periodo_visita: state.period,
    };

    const res = await fetch(`/avaliar/${bizSlug}/salvar`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    });

    if (!res.ok) throw new Error("Fail");
    const data = await res.json();

    if (state.photo && data.token) {
        await PhotoUploader.upload({
            file: state.photo,
            reviewToken: data.token,
            slug: bizSlug,
            csrfToken: '{{ csrf_token() }}'
        });
    }
}

function showSuccessScreen() {
    const screen = document.createElement('div');
    screen.className = 'success-screen';
    screen.innerHTML = `
        <div class="success-icon">✓</div>
        <h2 style="color:var(--text); margin-bottom:10px">${botConfig.lang.success}</h2>
        <p style="color:var(--text-muted); font-size:14px">Obrigado por nos ajudar a crescer!</p>
    `;
    document.querySelector('.phone').appendChild(screen);
    
    setTimeout(() => {
        if (window.opener) {
            window.close();
        } else {
            location.reload();
        }
    }, botConfig.config.auto_close);
}

// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW fail', err));
    });
}

init();
</script>
@endsection
