#!/bin/bash
# CP Review — Backup diário para Google Drive via rclone
# Executado pelo cron: 0 2 * * * /var/www/cpreview/backup.sh >> /var/log/cpreview-backup.log 2>&1
set -euo pipefail

# ── Configuração ────────────────────────────────────────────────────────────
APP_DIR="/var/www/cpreview"
BACKUP_TMP="/tmp/cp_backup_$$"          # $$ = PID, evita colisão entre runs
RCLONE_REMOTE="gdrive"
RCLONE_DEST="cp-review-backups"
RETENTION_DAYS=30
TIMESTAMP=$(date +%Y-%m-%d_%H-%M)

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

# ── Início ───────────────────────────────────────────────────────────────────
log "========== INÍCIO DO BACKUP =========="
mkdir -p "$BACKUP_TMP"

# ── 1. Banco de dados ────────────────────────────────────────────────────────
DB_HOST=$(env_val DB_HOST)
DB_PORT=$(env_val DB_PORT)
DB_DATABASE=$(env_val DB_DATABASE)
DB_USERNAME=$(env_val DB_USERNAME)
DB_PASSWORD=$(env_val DB_PASSWORD)

DB_FILE="$BACKUP_TMP/db_${TIMESTAMP}.sql.gz"
log "Exportando banco de dados: $DB_DATABASE ..."

# MYSQL_PWD evita que a senha apareça na lista de processos
export MYSQL_PWD="$DB_PASSWORD"
mysqldump \
    --host="${DB_HOST:-127.0.0.1}" \
    --port="${DB_PORT:-3306}" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --routines \
    --triggers \
    --set-gtid-purged=OFF \
    "$DB_DATABASE" | gzip -9 > "$DB_FILE"
unset MYSQL_PWD

log "DB dump gerado: $(du -sh "$DB_FILE" | cut -f1)"

# ── 2. Arquivos enviados (logos, capas) ─────────────────────────────────────
FILES_FILE="$BACKUP_TMP/files_${TIMESTAMP}.tar.gz"
STORAGE_PATH="$APP_DIR/storage/app/public"

if [ -d "$STORAGE_PATH" ]; then
    log "Arquivando arquivos de mídia ..."
    tar -czf "$FILES_FILE" -C "$APP_DIR/storage/app" public/ 2>/dev/null
    log "Files archive: $(du -sh "$FILES_FILE" | cut -f1)"
else
    log "Pasta de mídia não encontrada, pulando arquivos."
fi

# ── 3. Upload para Google Drive ───────────────────────────────────────────────
log "Enviando para ${RCLONE_REMOTE}:${RCLONE_DEST}/ ..."
rclone copy "$BACKUP_TMP/" "${RCLONE_REMOTE}:${RCLONE_DEST}/" \
    --log-level INFO

# ── 4. Limpeza de backups antigos no Drive ────────────────────────────────────
log "Removendo backups com mais de ${RETENTION_DAYS} dias no Drive ..."
rclone delete "${RCLONE_REMOTE}:${RCLONE_DEST}/" \
    --min-age "${RETENTION_DAYS}d" \
    --log-level INFO || true

# ── 5. Limpeza local ─────────────────────────────────────────────────────────
rm -rf "$BACKUP_TMP"

log "========== BACKUP CONCLUÍDO: db_${TIMESTAMP}.sql.gz =========="
