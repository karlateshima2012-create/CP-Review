#!/bin/bash
# CP Review — Monitoramento de Saúde da VPS e Notificações via Telegram
# Executado pelo cron: */5 * * * * /var/www/cpreview/monitor.sh >> /var/log/cpreview-monitor.log 2>&1
set -euo pipefail

APP_DIR="/var/www/cpreview"
HOSTNAME=$(hostname)

# ── Helpers ─────────────────────────────────────────────────────────────────
log() { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"; }

# Lê valor de uma chave do arquivo .env do Laravel (sem aspas)
env_val() {
    local raw
    raw=$(grep -E "^${1}=" "$APP_DIR/.env" 2>/dev/null | head -1 | cut -d'=' -f2-)
    raw="${raw%\"}" ; raw="${raw#\"}"
    raw="${raw%\'}" ; raw="${raw#\'}"
    echo "$raw"
}

# Carrega credenciais do Telegram
TELEGRAM_BOT_TOKEN=$(env_val TELEGRAM_BOT_TOKEN)
TELEGRAM_CHAT_ID=$(env_val TELEGRAM_CHAT_ID)

if [ -z "${TELEGRAM_BOT_TOKEN}" ] || [ -z "${TELEGRAM_CHAT_ID}" ]; then
    log "Erro: TELEGRAM_BOT_TOKEN ou TELEGRAM_CHAT_ID não estão configurados no arquivo .env"
    exit 1
fi

send_telegram() {
    local message="$1"
    curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
        -d "chat_id=${TELEGRAM_CHAT_ID}" \
        -d "text=${message}" \
        -d "parse_mode=HTML" > /dev/null || log "Falha ao enviar notificação para o Telegram"
}

# ── 1. Verificar Espaço em Disco ──────────────────────────────────────────────
# Verifica a partição raiz (/) e alerta se o uso for maior que 85%
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | cut -d'%' -f1)
if [ "$DISK_USAGE" -gt 85 ]; then
    log "⚠️ Alerta de Disco: ${DISK_USAGE}% em uso."
    send_telegram "⚠️ <b>[Alerta VPS]</b> Espaço em disco elevado no servidor <code>${HOSTNAME}</code>: <b>${DISK_USAGE}%</b> ocupado!"
fi

# ── 2. Verificar Memória RAM ──────────────────────────────────────────────────
# Alerta se o uso de RAM for maior que 90%
MEM_USAGE=$(free | grep Mem | awk '{print $3/$2 * 100.0}' | cut -d'.' -f1)
if [ "$MEM_USAGE" -gt 90 ]; then
    log "⚠️ Alerta de Memória: ${MEM_USAGE}% em uso."
    send_telegram "⚠️ <b>[Alerta VPS]</b> Uso de memória RAM elevado no servidor <code>${HOSTNAME}</code>: <b>${MEM_USAGE}%</b> ocupado!"
fi

# ── 3. Verificar Uso de CPU ───────────────────────────────────────────────────
# Alerta se a carga média de CPU (1 minuto) for muito alta (> 90%)
CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2 + $4}' | cut -d'.' -f1 || echo 0)
if [ "$CPU_USAGE" -gt 90 ]; then
    log "⚠️ Alerta de CPU: ${CPU_USAGE}% em uso."
    send_telegram "⚠️ <b>[Alerta VPS]</b> Uso de CPU elevado no servidor <code>${HOSTNAME}</code>: <b>${CPU_USAGE}%</b> em uso!"
fi

# ── 4. Verificar Status de Serviços Críticos ──────────────────────────────────
# Verifica se Nginx, MySQL e PHP-FPM estão ativos. Se inativos, tenta reiniciar.
for SERVICE in nginx mysql php8.4-fpm; do
    if ! systemctl is-active --quiet "$SERVICE"; then
        log "🔴 Serviço ${SERVICE} INATIVO! Tentando reiniciar..."
        send_telegram "🔴 <b>[Crítico VPS]</b> O serviço <b>${SERVICE}</b> caiu no servidor <code>${HOSTNAME}</code>! Tentando restabelecer..."
        
        # Tenta reiniciar o serviço
        if systemctl restart "$SERVICE" 2>/dev/null; then
            sleep 2
            if systemctl is-active --quiet "$SERVICE"; then
                log "🟢 Serviço ${SERVICE} reiniciado com sucesso."
                send_telegram "🟢 <b>[Recuperado VPS]</b> O serviço <b>${SERVICE}</b> foi reiniciado e restabelecido com sucesso no servidor <code>${HOSTNAME}</code>."
            else
                log "🔴 Falha ao restabelecer o serviço ${SERVICE} após reinicialização."
                send_telegram "❌ <b>[Falha VPS]</b> Não foi possível restabelecer o serviço <b>${SERVICE}</b> no servidor <code>${HOSTNAME}</code> após tentativa de reinicialização!"
            fi
        else
            log "🔴 Permissão negada ou falha ao reiniciar o serviço ${SERVICE}."
            send_telegram "❌ <b>[Falha VPS]</b> Falha ao executar o comando de reinício do serviço <b>${SERVICE}</b> no servidor <code>${HOSTNAME}</code>!"
        fi
    fi
done

log "Verificação de rotina de saúde concluída com sucesso."
