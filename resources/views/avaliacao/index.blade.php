@extends('layouts.app')

@section('title', 'Avaliação')

@section('meta')
    <!-- Open Graph / WhatsApp Preview Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'CP Review') }}">
    <meta property="og:url" content="{{ route('avaliacao.show', $cliente->slug) }}">
@endsection

@section('content')
@php
    $cor = $cliente->cor_principal ?? '#7C3AED';
    $r = hexdec(substr($cor, 1, 2));
    $g = hexdec(substr($cor, 3, 2));
    $b = hexdec(substr($cor, 5, 2));
    $corLight = sprintf('#%02x%02x%02x', min(255,$r+40), min(255,$g+40), min(255,$b+40));
@endphp
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
  --bubble-user: {{ $cor }};
  --bubble-user-dark: {{ $cor }};
  --text: #111827;
  --text-muted: #4B5563;
  --text-dim: #9CA3AF;
  --accent: {{ $cor }};
  --accent-glow: rgba({{ $r }}, {{ $g }}, {{ $b }}, 0.15);
  --header-bg: linear-gradient(135deg, {{ $cor }} 0%, {{ $corLight }} 100%);
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
  border: 4px solid #FFFFFF;
}

@media (min-width: 500px) {
    .phone {
        height: 844px;
        border-radius: 40px;
        border: 12px solid #FFFFFF;
        box-shadow: 0 30px 60px -10px rgba(0,0,0,0.5), 0 0 0 1px rgba(0,0,0,0.2);
    }
}

/* HEADER */
.header {
  flex-shrink: 0;
  height: 160px;
  position: relative;
  border-bottom: 1px solid var(--border);
  z-index: 20;
  color: #FFFFFF;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  background: var(--header-bg);
  overflow: hidden;
  display: flex;
  align-items: center;
}

.header-cover {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  z-index: 1;
}

.header-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.75) 100%);
  z-index: 2;
}

.header-content {
  position: relative;
  width: 100%;
  padding: 0 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  z-index: 3;
}

.biz-avatar {
  width: 76px; height: 76px;
  border-radius: 16px;
  background: #FFFFFF;
  display: flex; align-items: center; justify-content: center;
  font-size: 38px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  overflow: hidden;
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.biz-info { flex: 1; text-align: left; }
.biz-name { font-size: 18px; font-weight: 800; color: #FFFFFF; line-height: 1.2; letter-spacing: 0.5px; text-shadow: 0 4px 12px rgba(0, 0, 0, 0.6), 0 1px 2px rgba(0, 0, 0, 0.8); }
.biz-status { font-size: 12px; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 5px; font-weight: 500; }
.biz-status::before { content:''; width:6px; height:6px; background:#10B981; border-radius:50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }

.close-btn {
  font-size: 18px;
  color: #FFF;
  cursor: pointer;
  opacity: 0.7;
  padding: 10px;
  border-radius: 50%;
  background: rgba(0,0,0,0.3);
  line-height: 1;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: auto;
  border: none;
}
.close-btn:hover {
  opacity: 1;
  background: rgba(0,0,0,0.5);
}

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
    filter: grayscale(1) opacity(0.8);
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
    background: var(--surface);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 100;
    text-align: center;
    padding: 40px;
    animation: fadeIn 0.3s ease-out forwards;
}
.success-icon {
    width: 90px; height: 90px;
    background: var(--accent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 45px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px var(--accent-transparent);
    color: #FFF;
    animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.success-title {
    font-family: 'Bebas Neue', cursive;
    font-size: 36px;
    color: var(--accent);
    margin-bottom: 10px;
    letter-spacing: 1px;
}
@keyframes scaleIn {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
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
            @if($cliente->cover_path)
                <div class="header-cover" style="background-image: url('{{ asset('storage/' . $cliente->cover_path) }}')"></div>
                <div class="header-overlay"></div>
            @else
                <div class="header-overlay" style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 100%);"></div>
            @endif
            
            <div class="header-content">
                <div class="biz-avatar">
                    @if($cliente->logo_path)
                        <img src="{{ asset('storage/' . $cliente->logo_path) }}" alt="{{ $cliente->nome_empresa }}" style="width:100%; height:100%; object-fit:contain; border-radius:15px; padding:0;">
                    @else
                        🏢
                    @endif
                </div>
                <div class="biz-info">
                    <div class="biz-name" id="header-biz-name">{{ $cliente->nome_empresa }}</div>
                    <div class="biz-status">Online</div>
                </div>
                <button type="button" class="close-btn" onclick="showSuccessScreen()">
                    ✕
                </button>
            </div>
        </div>

        <div class="chat-area" id="chat"></div>

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
    if (botConfig.lang.welcome && botConfig.lang.welcome.step !== null && botConfig.lang.welcome.step !== '') {
        await addBotMsg(botConfig.lang.welcome.text);
        await wait(400);
    }
    await askFirstVisit();
}

async function askFirstVisit() {
    if (!botConfig.lang.q_first_visit || botConfig.lang.q_first_visit.step === null || botConfig.lang.q_first_visit.step === '') {
        state.first_visit = null;
        await askRating();
        return;
    }
    await addBotMsg(botConfig.lang.q_first_visit.text);
    const div = document.createElement('div');
    div.className = 'qr-row';
    div.innerHTML = `
        <button class="qr-btn" onclick="handleFirstVisit(true)">${botConfig.lang.btn_yes}</button>
        <button class="qr-btn" onclick="handleFirstVisit(false)">${botConfig.lang.btn_no}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handleFirstVisit = async (val) => {
    state.first_visit = val;
    if (event && event.target && event.target.closest('.qr-row')) {
        event.target.closest('.qr-row').remove();
    }
    if (val !== null) {
        addUserMsg(val ? botConfig.lang.btn_yes : botConfig.lang.btn_no);
        await wait(400);
    }
    if (botConfig.lang.first_visit_ack && botConfig.lang.first_visit_ack.step !== null && botConfig.lang.first_visit_ack.step !== '') {
        await addBotMsg(botConfig.lang.first_visit_ack.text);
        await wait(400);
    }
    await askRating();
};

async function askRating() {
    if (botConfig.lang.askRate && botConfig.lang.askRate.step !== null && botConfig.lang.askRate.step !== '') {
        const lines = botConfig.lang.askRate.text.split('\n');
        for (const line of lines) {
            await addBotMsg(line);
            await wait(400);
        }
    }
    const div = document.createElement('div');
    div.className = 'star-rating';
    div.innerHTML = [1,2,3,4,5].map(i => `<span class="star-item" onclick="handleRating(${i})">⭐</span>`).join('');
    chat.appendChild(div);
    scrollChat();
}

window.handleRating = async (r) => {
    state.rating = r;
    if (event && event.target && event.target.closest('.star-rating')) {
        event.target.closest('.star-rating').remove();
    }
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
    if (botConfig.lang.highRate && botConfig.lang.highRate.step !== null && botConfig.lang.highRate.step !== '') {
        await addBotMsg(botConfig.lang.highRate.text);
        await wait(300);
    }
    await askPeriod(true);
}

// ==========================================
// FLUXO NEGATIVO 
// ==========================================
async function handleNegativeFlow() {
    if (botConfig.lang.lowRate && botConfig.lang.lowRate.step !== null && botConfig.lang.lowRate.step !== '') {
        await addBotMsg(botConfig.lang.lowRate.text);
        await wait(300);
    }
    if (botConfig.lang.lowRateQ && botConfig.lang.lowRateQ.step !== null && botConfig.lang.lowRateQ.step !== '') {
        await addBotMsg(botConfig.lang.lowRateQ.text);
    }

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
    if (event && event.target && event.target.closest('.problems-grid')) {
        event.target.closest('.problems-grid').remove();
    }
    addUserMsg(opt);
    await wait(300);
    await askFeedbackText();
};

async function askFeedbackText() {
    if (!botConfig.lang.q_optional_text || botConfig.lang.q_optional_text.step === null || botConfig.lang.q_optional_text.step === '') {
        await askPhoto();
        return;
    }
    await addBotMsg(botConfig.lang.q_optional_text.text);
    const div = document.createElement('div');
    div.className = 'input-container';
    div.innerHTML = `
        <textarea id="feedback-area" class="f-input" rows="2" placeholder="${botConfig.lang.feedback_placeholder || 'Sua opinião...'}"></textarea>
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
        if (document.querySelector('.input-container')) {
            document.querySelector('.input-container').remove();
        }
        await wait(300);
        await askPhoto();
    } else {
        addUserMsg(botConfig.lang.btn_skip);
        if (document.querySelector('.input-container')) {
            document.querySelector('.input-container').remove();
        }
        await wait(300);
        await finishChat(false);
    }
};

async function askPhoto() {
    if (!botConfig.lang.q_optional_photo || botConfig.lang.q_optional_photo.step === null || botConfig.lang.q_optional_photo.step === '') {
        await askContact();
        return;
    }
    await addBotMsg(botConfig.lang.q_optional_photo.text);
    const div = document.createElement('div');
    div.className = 'input-container';
    div.innerHTML = `
        <div class="photo-upload">
            <input type="file" id="photo-input" hidden accept="image/*" onchange="previewPhoto(this)">
            <label for="photo-input" class="upload-label">📸 Adicionar Foto</label>
            <img id="photo-preview" class="photo-preview">
        </div>
        <div style="display:flex; gap:10px; margin-top:10px">
            <button class="confirm-btn" style="background:var(--surface2); color:var(--text)" onclick="handlePhotoSubmit(false)">${botConfig.lang.btn_skip}</button>
            <button class="confirm-btn" onclick="handlePhotoSubmit(true)">${botConfig.lang.btn_feedback_send}</button>
        </div>
    `;
    document.querySelector('.phone').appendChild(div);
    scrollChat();
}

window.handlePhotoSubmit = async (hasValue) => {
    if (hasValue && state.photo) {
        addUserMsg("🖼️ Foto anexada");
    } else {
        state.photo = null;
        addUserMsg(botConfig.lang.btn_skip);
    }
    if (document.querySelector('.input-container')) {
        document.querySelector('.input-container').remove();
    }
    await wait(300);
    if (botConfig.lang.photo_ack && botConfig.lang.photo_ack.step !== null && botConfig.lang.photo_ack.step !== '') {
        await addBotMsg(botConfig.lang.photo_ack.text);
        await wait(300);
    }
    await askContact();
};

async function askPeriod(isPos) {
    if (!botConfig.lang.q_period || botConfig.lang.q_period.step === null || botConfig.lang.q_period.step === '') {
        state.period = null;
        if (isPos) {
            await askRecommend();
        } else {
            await askContact();
        }
        return;
    }
    await addBotMsg(botConfig.lang.q_period.text);
    const div = document.createElement('div');
    div.className = 'options-grid';
    div.innerHTML = `
        <button class="qr-btn" onclick="handlePeriod('morning', ${isPos})">${botConfig.lang.btn_morning}</button>
        <button class="qr-btn" onclick="handlePeriod('afternoon', ${isPos})">${botConfig.lang.btn_afternoon}</button>
        <button class="qr-btn" onclick="handlePeriod('night', ${isPos})">${botConfig.lang.btn_night}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handlePeriod = async (p, isPos) => {
    state.period = p;
    if (event && event.target && event.target.closest('.options-grid')) {
        event.target.closest('.options-grid').remove();
    }
    const labels = { morning: botConfig.lang.btn_morning, afternoon: botConfig.lang.btn_afternoon, night: botConfig.lang.btn_night };
    addUserMsg(labels[p]);
    await wait(300);
    
    if (isPos) {
        await askRecommend();
    } else {
        await askContact();
    }
};

// ==========================================
// FINALIZAÇÃO POSITIVA
// ==========================================
async function askRecommend() {
    if (!botConfig.lang.q_recommend || botConfig.lang.q_recommend.step === null || botConfig.lang.q_recommend.step === '') {
        await finishChat(true);
        return;
    }
    await addBotMsg(botConfig.lang.q_recommend.text);
    await wait(300);

    const gDiv = document.createElement('div');
    gDiv.style.padding = '5px 0';
    gDiv.innerHTML = `
        <a href="${botConfig.tenant.google_link}" target="_blank" onclick="handleGoogleClick()" class="confirm-btn" style="background:var(--accent); text-decoration:none">
            ${botConfig.lang.googleBtn || '⭐ Avaliar no Google'}
        </a>
    `;
    chat.appendChild(gDiv);
    scrollChat();
    await wait(800);

    await finishChat(true, true);
}

// Legacy compatibility
window.handleRecommend = async (rec) => {
    if (event && event.target && event.target.closest('.options-grid')) {
        event.target.closest('.options-grid').remove();
    }
    const map = { yes: botConfig.lang.btn_rec_yes, maybe: botConfig.lang.btn_rec_maybe, no: botConfig.lang.btn_rec_no };
    addUserMsg(map[rec]);
    await wait(400);
    await finishChat(true);
};

// ==========================================
// FINALIZAÇÃO NEGATIVA
// ==========================================
async function askContact() {
    // Mensagem informativa: "feedback encaminhado" (antes da pergunta de contato)
    if (botConfig.lang.feedback_sent && botConfig.lang.feedback_sent.step !== null && botConfig.lang.feedback_sent.step !== '') {
        await addBotMsg(botConfig.lang.feedback_sent.text);
        await wait(600);
    }

    if (!botConfig.lang.q_contact || botConfig.lang.q_contact.step === null || botConfig.lang.q_contact.step === '') {
        await finishChat(false);
        return;
    }
    await addBotMsg(botConfig.lang.q_contact.text);
    const div = document.createElement('div');
    div.className = 'qr-row';
    div.innerHTML = `
        <button class="qr-btn" onclick="handleContactStep(true)">${botConfig.lang.btn_contact_yes}</button>
        <button class="qr-btn" onclick="handleContactStep(false)">${botConfig.lang.btn_contact_no}</button>
    `;
    chat.appendChild(div);
    scrollChat();
}

window.handleContactStep = async (wantsContact) => {
    if (event && event.target && event.target.closest('.qr-row')) {
        event.target.closest('.qr-row').remove();
    }
    addUserMsg(wantsContact ? botConfig.lang.btn_contact_yes : botConfig.lang.btn_contact_no);
    await wait(400);

    if (!wantsContact) {
        state.contact = '';
        state.tipo_contato = 'nao';
        await finishChat(false);
    } else {
        await askContactChannel();
    }
};

async function askContactChannel() {
    const div = document.createElement('div');
    div.className = 'options-grid';
    if (botConfig.config.locale === 'pt') {
        div.innerHTML = `
            <button class="qr-btn" onclick="handleContactChannel('whatsapp')">${botConfig.lang.btn_choose_wa}</button>
            <button class="qr-btn" onclick="handleContactChannel('email')">${botConfig.lang.btn_choose_email}</button>
        `;
    } else {
        div.innerHTML = `
            <button class="qr-btn" onclick="handleContactChannel('line')">${botConfig.lang.btn_choose_line}</button>
            <button class="qr-btn" onclick="handleContactChannel('email')">${botConfig.lang.btn_choose_email}</button>
        `;
    }
    chat.appendChild(div);
    scrollChat();
}

window.handleContactChannel = async (channel) => {
    if (event && event.target && event.target.closest('.options-grid')) {
        event.target.closest('.options-grid').remove();
    }
    const labelMap = {
        whatsapp: botConfig.lang.btn_choose_wa,
        line: botConfig.lang.btn_choose_line,
        email: botConfig.lang.btn_choose_email
    };
    addUserMsg(labelMap[channel]);
    await wait(400);

    const div = document.createElement('div');
    div.className = 'input-container';
    
    let placeholder = "Seu contato...";
    if (channel === 'email') {
        placeholder = botConfig.config.locale === 'pt' ? "Seu melhor e-mail..." : "メールアドレスを入力してください...";
    } else if (channel === 'whatsapp') {
        placeholder = "DDD + Número...";
    } else if (channel === 'line') {
        placeholder = "LINE ID...";
    }

    div.innerHTML = `
        <input type="text" id="contact-val" class="f-input" placeholder="${placeholder}">
        <button class="confirm-btn" onclick="submitContact('${channel}')">${botConfig.lang.btn_send_txt} ➔</button>
    `;
    document.querySelector('.phone').appendChild(div);
    scrollChat();
};

window.submitContact = async (channel) => {
    state.contact = document.getElementById('contact-val').value;
    state.tipo_contato = channel;
    if (document.querySelector('.input-container')) {
        document.querySelector('.input-container').remove();
    }
    addUserMsg(state.contact || 'Ok');
    await wait(300);
    await finishChat(false);
};

// Legacy compatibility
window.handleContactChoice = async (c) => {
    if (c === 'no') {
        await handleContactStep(false);
    } else {
        await handleContactStep(true);
        await handleContactChannel(c);
    }
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
        <a href="${botConfig.tenant.google_link}" target="_blank" onclick="handleGoogleClick()" class="confirm-btn" style="background:var(--accent); text-decoration:none">
            ${botConfig.lang.googleBtn}
        </a>
    `;
    chat.appendChild(gDiv);
    scrollChat();
}

window.handleGoogleClick = () => {
    setTimeout(() => {
        showSuccessScreen(true);
    }, 1500); 
};

let isSubmitting = false;
async function finishChat(isPos, googleBtnShown = false) {
    if(isSubmitting) return;
    isSubmitting = true;

    submitEvaluation().catch(e => console.error(e));

    if (isPos && !googleBtnShown) {
        await showGoogleBtn(null, true);
        await wait(800);
    }

    const finalConfig = isPos ? botConfig.lang.highFinalMsg : botConfig.lang.lowFinalMsg;
    if (finalConfig && finalConfig.step !== null && finalConfig.step !== '') {
        const finalTexts = finalConfig.text.split('\n');
        for (const text of finalTexts) {
            await addBotMsg(text);
            await wait(600);
        }
    }

    // Append a manual close button so they have time to click Google or just exit
    const closeDiv = document.createElement('div');
    closeDiv.style.textAlign = 'center';
    closeDiv.style.marginTop = '20px';
    closeDiv.innerHTML = `
        <button onclick="showSuccessScreen()" style="background:var(--surface2); border:1px solid var(--border); border-radius:20px; color:var(--text-dim); font-size:13px; cursor:pointer; padding:8px 16px; font-weight:600">
            ✕ SAIR
        </button>
    `;
    chat.appendChild(closeDiv);
    scrollChat();
}

async function submitEvaluation() {
    const payload = {
        nota: state.rating,
        feedback: state.feedback,
        problema: state.problem || state.aspect,
        tipo_contato: state.tipo_contato || (state.contact ? 'whatsapp' : 'nao'),
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

function showSuccessScreen(fastClose = false) {
    const screen = document.createElement('div');
    screen.className = 'success-screen';
    screen.innerHTML = `
        <button onclick="window.close()" style="
            position:absolute; top:16px; right:16px;
            width:36px; height:36px; border-radius:50%;
            border:none; background:rgba(0,0,0,0.08);
            cursor:pointer; font-size:18px; line-height:1;
            color:var(--text-muted); display:flex; align-items:center; justify-content:center;
        " aria-label="Fechar">✕</button>
        <div class="success-icon">✓</div>
        <h2 class="success-title">MUITO OBRIGADO!</h2>
        <p style="color:var(--text); font-size:16px; line-height:1.5">${botConfig.lang.success || "Sua avaliação nos ajuda a crescer e melhorar todos os dias."}</p>
    `;
    document.querySelector('.phone').appendChild(screen);

    const delay = fastClose ? 1500 : botConfig.config.auto_close;
    setTimeout(() => {
        try {
            window.close();
        } catch(e) {}

        const exitMsg = document.createElement('p');
        exitMsg.style.color = 'var(--text-muted)';
        exitMsg.style.fontSize = '14px';
        exitMsg.style.marginTop = '20px';
        exitMsg.style.animation = 'fadeIn 0.5s';
        exitMsg.innerHTML = "Você já pode fechar ou minimizar o aplicativo.";
        screen.appendChild(exitMsg);

    }, delay);
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
