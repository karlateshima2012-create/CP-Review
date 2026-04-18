# DIRETRIZES TÉCNICAS E ARQUITETURA — CP REVIEW SAAS

Este documento consolida as especificações de arquitetura e as diretrizes de implementação sênior para o projeto CP Review. Ele serve como o guia definitivo para o desenvolvimento da plataforma, abrangendo desde a visão de alto nível até snippets de código prontos para produção.

---

## ══ 01 — VISÃO GERAL E AVALIAÇÃO DO PROJETO ══

### Diagnóstico Técnico
A visão do produto é **genuinamente sólida**. A interface conversacional PWA resolve um problema real melhor do que qualquer alternativa existente no segmento. O modelo de negócio é sustentável e a stack técnica (Laravel + Vanilla JS) é a decisão correta.

#### Avaliação de Dimensões
| Dimensão | O que existe | O que falta / precisa melhorar | Status |
| :--- | :--- | :--- | :--- |
| **Interface conversacional (PWA)** | Demo funcional com fluxo alto/baixo, typing indicator, bolhas, Google CTA | Sem detecção de idioma, sem campo de foto, sem pergunta de contexto (1ª visita / turno), sem Service Worker offline | **Gap Médio** |
| **Multi-tenancy** | Conceito mencionado, UUIDs citados | Isolamento de dados não especificado. Falta definir políticas Laravel (Gates/Policies). | **Crítico** |
| **Modelo de dados** | Tabelas clientes + avaliações (versão PHP simples) | Schema insuficiente. Falta: tenants, users, bots_config, notifications, sessions, media, audit_log | **Crítico** |
| **Autenticação e roles** | Login simples no painel admin | Falta sistema de roles: Super Admin / Lojista / (Consumidor anônimo). | **Crítico** |
| **Personalização do bot** | Mencionado como feature | Sem especificação de armazenamento e edição do roteiro. | **Gap Médio** |
| **Notificações (WhatsApp / LINE)** | Evolution API + LINE Notify citados | Evolution API instável. LINE Notify descontinuado (usar LINE Messaging API). Fallback necessário. | **Risco** |
| **Dashboard de BI** | Conceito de mapa de calor e métricas | Nenhuma tela especificada. Sem queries definidas. | **Gap Médio** |
| **Closing the Loop** | Mencionado como feature | Fluxo não especificado. Necessário identificador anônimo de sessão. | **Gap Médio** |
| **Segurança** | UUID, Rate Limiting, CSRF | Proteção de upload, validação de MIME, prevenção de XSS. | **Risco** |
| **Offline / Service Worker** | Mencionado como diferencial | Não implementado. Estratégia de cache e sync queue não definida. | **Crítico** |

> [!CAUTION]
> **Alerta Crítico — LINE Notify:** O LINE Notify foi oficialmente descontinuado em março de 2024. A solução correta para o Japão é a **LINE Messaging API**.

---

## ══ 02 — ARQUITETURA DA SOLUÇÃO ══

### Camadas do Sistema

#### 1. Camada de Apresentação
- **PWA Chat (Consumidor):** Vanilla JS, Service Worker, Offline Cache, IndexedDB.
- **Painel Admin (Super Admin):** Laravel Blade / Livewire. Gestão de tenants, BI Global.
- **Painel Lojista:** Laravel Blade. BI, Bot Config.

#### 2. Camada de Aplicação (Laravel 11)
- **API Routes (Sanctum):** `/api/review`, `/api/bot-script/{slug}`, `/api/media/upload`.
- **Web Routes (Session):** `/painel/*`, `/admin/*`.
- **Jobs / Queues:** `NotifyLowRating`, `SendMonthlyReport`, `SyncOfflineReview`.
- **Middleware:** `TenantResolver`, `RateLimit`, `CORS`, `CSRF`.

#### 3. Camada de Domínio
- `ReviewService`, `BotScriptService`, `NotificationService`, `ReportService`, `TenantService`, `MediaService`, `LoopService`, `QRCodeService`.

#### 4. Camada de Dados
- **MySQL:** Shared DB + `tenant_id`.
- **Redis:** Queue, Cache (Bot Script, Reports).
- **Storage:** S3 ou Local (Fotos, QR Codes, Reports PDF).

### Integrações Externas
- **WhatsApp (BR):** Evolution API ou Meta Cloud. Fallback SMTP.
- **LINE Messaging API (JP):** LINE Bot Channel. Fallback SMTP.
- **SMTP (Relatórios):** Mailgun / SES.

---

## ══ 03 — MODELO DE DADOS E ISOLAMENTO ══

### Decisão Arquitetural: Multi-Tenancy
Usar **Shared Database com tenant_id** em todas as tabelas. É a abordagem correta para o estágio atual: simples, performática e fácil de migrar. Cada tenant é identificado pelo UUID.

### Schema Principal (MySQL)

#### Tabela: `tenants` (CORE)
- `id`: UUID PK
- `slug`: VARCHAR UNIQUE
- `name`: VARCHAR
- `google_place_id`: VARCHAR
- `locale`: ENUM (pt, ja)
- `plan`: ENUM (lite, standard)
- `notify_channel`: ENUM (whatsapp, line, email)
- `notify_target`: VARCHAR
- `active`: BOOLEAN
- `trial_ends_at`: TIMESTAMP

#### Tabela: `users` (AUTH)
- `id`: UUID PK
- `tenant_id`: UUID FK NULL
- `name`: VARCHAR
- `email`: VARCHAR UNIQUE (IDX)
- `password`: VARCHAR HASHED
- `role`: ENUM (super_admin, owner)

#### Tabela: `bot_scripts` (CONFIG)
- `id`: UUID PK
- `tenant_id`: UUID FK
- `locale`: ENUM (pt, ja)
- `welcome_msg`: TEXT
- `high_msg`: TEXT (Nota 4-5)
- `low_msg`: TEXT (Nota 1-3)
- `issues_json`: JSON (Grid de problemas)
- `aspects_json`: JSON (Quick replies de elogio)

#### Tabela: `reviews` (CORE)
- `id`: UUID PK
- `tenant_id`: UUID FK IDX
- `session_token`: VARCHAR IDX
- `rating`: TINYINT (1-5)
- `issue_category`: VARCHAR NULL
- `feedback_text`: TEXT NULL
- `is_first_visit`: BOOLEAN NULL
- `visit_period`: ENUM (lunch, dinner, other)
- `contact_email`: VARCHAR NULL (Encrypted)
- `status`: ENUM (new, seen, resolved)
- `created_at`: TIMESTAMP IDX

#### Tabela: `media` (UPLOADS)
- `id`: UUID PK
- `review_id`: UUID FK
- `tenant_id`: UUID FK
- `path`: VARCHAR
- `mime_type`: VARCHAR
- `size_bytes`: INT

---

## ══ 04 — IMPLEMENTAÇÕES SENIOR (PRONTO PARA PRODUÇÃO) ══

### A. Proteção Cross-Tenant (TenantModel + Global Scope)
**Por que é crítico:** Em um Shared Database, um único `Review::all()` sem filtro expõe dados de todos os tenants. A solução é uma classe base que injeta o `tenant_id` automaticamente.

#### `app/Models/TenantModel.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTenant;
use App\Models\Scopes\TenantScope;

abstract class TenantModel extends Model
{
    use HasTenant;

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                $tenantId = app('current.tenant.id');

                if (! $tenantId) {
                    throw new \RuntimeException(
                        'Tentativa de criar registro sem tenant_id resolvido.'
                    );
                }

                $model->tenant_id = $tenantId;
            }
        });
    }

    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}
```

#### `app/Models/Scopes/TenantScope.php`
```php
<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\{Builder, Model, Scope};

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = app('current.tenant.id');

        if ($tenantId) {
            $builder->where(
                $model->getTable() . '.tenant_id',
                $tenantId
            );
        }
    }
}
```

---

### B. Notificações com Fallback Triplo
**Estratégia:** Abstração via interface permite trocar drivers (Evolution para Meta) sem mexer no core do sistema.

#### `app/Services/NotificationService.php`
```php
<?php

namespace App\Services;

use App\Models\{Review, NotificationLog, Tenant};
use App\Services\Notification\Contracts\WhatsAppDriver;
use App\Services\Notification\LineDriver;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private readonly WhatsAppDriver $whatsapp,
        private readonly LineDriver     $line,
    ) {}

    public function notifyLowRating(Review $review): void
    {
        $tenant  = $review->tenant;
        $message = $this->buildMessage($review, $tenant);
        $sent    = false;
        $attempt = 0;

        while (! $sent && $attempt < 3) {
            $attempt++;

            $sent = match ($tenant->notify_channel) {
                'whatsapp' => $this->whatsapp->send($tenant->notify_target, $message),
                'line'     => $this->line->send($tenant->notify_target, $message),
                default    => false,
            };

            $this->logAttempt($review, $tenant->notify_channel, $sent, $attempt);

            if (! $sent) sleep(2);
        }

        if (! $sent) {
            $this->fallbackEmail($review, $tenant);
        }
    }

    private function buildMessage(Review $review, Tenant $tenant): string
    {
        $stars    = str_repeat('⭐', $review->rating);
        $category = $review->issue_category ?? 'Não informado';
        $link     = route('painel.reviews.show', $review->id);

        return $tenant->locale === 'ja'
            ? "【CP Review】低評価が届きました\n評価: {$stars}\nカテゴリ: {$category}\n詳細: {$link}"
            : "⚠️ Nova avaliação negativa!\nNota: {$stars}\nProblema: {$category}\nVer detalhes: {$link}";
    }
}
```

---

### C. BI Pré-Agregação (Performance)
**Processamento:** Job diário para alimentar dashboards instantâneos.

#### `app/Console/Commands/AggregateDailyMetrics.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Tenant, Review, DailyMetricsSummary};
use Illuminate\Support\Carbon;

class AggregateDailyMetrics extends Command
{
    protected $signature = 'metrics:aggregate {--date= : Data no formato Y-m-d}';

    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();

        Tenant::where('active', true)->chunkById(50, function ($tenants) use ($date) {
            foreach ($tenants as $tenant) {
                $this->aggregateTenant($tenant, $date);
            }
        });

        return Command::SUCCESS;
    }

    private function aggregateTenant(Tenant $tenant, Carbon $date): void
    {
        // ... (agregação por períodos lunch/dinner/all)
        // Salva em daily_metrics_summary via upsert()
    }
}
```

---

### D. PWA: Retry UI para Foto Offline
**Persistência:** Fila em `IndexedDB` para suportar arquivos binários (Blobs) de até 5MB.

#### `public/pwa/offline-queue.js`
```javascript
const OfflineQueue = {
  DB_NAME: 'cp_review_queue',
  STORE: 'pending_uploads',

  async enqueue({ reviewToken, slug, photoBlob, photoName }) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx  = db.transaction(this.STORE, 'readwrite');
      const req = tx.objectStore(this.STORE).add({
        reviewToken, slug, photoBlob, photoName,
        status: 'pending',
        attempts: 0,
        createdAt: new Date().toISOString(),
      });
      req.onsuccess = () => resolve(req.result);
      req.onerror = () => reject(req.error);
    });
  },

  async updateStatus(id, status, extras = {}) {
    // ... atualiza status na fila para retry ou conclusão
  }
};
```

---

## ══ 05 — PAPÉIS E FUNCIONALIDADES ══

### 🛡️ Super Admin (Você) — `/admin`
- **Dashboard Global:** MRR, tenants ativos, falhas críticas de notificação.
- **Gestão de Tenants:** CRUD completo, ativação de planos, logos.
- **Gerador de QR Code:** Exportação PNG/PDF com logo centralizado.
- **Monitor de Notificações:** Log em tempo real das mensagens enviadas.

### 🏪 Lojista — `/painel`
- **Insights:** Mapa de calor de notas baixas (dias x períodos), tendência semanal.
- **Avaliações:** Lista detalhada, fotos anexadas, gestão de status (resolvido).
- **Configuração do Bot:** Edição de roteiro, escolha de tom (Casual/Keigo).
- **Meu QR Code:** Download e link direto para o PWA.

### 📱 Consumidor — `/r/{slug}`
- **PWA Ultra-rápido:** Carregamento em <1s via Service Worker.
- **Inteligência:** Detecção automática de idioma (PT/JA).
- **Fluxo Dual:** 
  - **Nota Alta:** Quick replies de elogio + link para Google Maps.
  - **Nota Baixa:** Grid de problemas + feedback texto + foto anexa.

---

## ══ 06 — ROADMAP DE EXECUÇÃO ══

| Sprint | Foco | Entregável |
| :--- | :--- | :--- |
| **01** | Fundação | Migrations, Multi-tenancy Scope, Auth Super Admin. |
| **02** | Core API | Endpoints de feedback, salvamento de fotos, isolamento tenant. |
| **03** | PWA | Interface conversacional, suporte Offline, detecção de idioma. |
| **04** | Painel Admin | CRUD Tenants, Gerador de QR Code PDF. |
| **05** | Notificações | Integração Evolution API + LINE Messaging API + Jobs. |
| **06** | Painel Lojista | BI Básico, Lista de Avaliações, Closing the Loop. |
| **07** | BI & Relatórios | Mapa de Calor, Consolidação diária, Relatório Mensal e-mail. |
| **08** | Polish | Configurador visual do bot, Hardening de segurança, Testes. |

---

## ══ 07 — SEGURANÇA E PERFORMANCE ══

### Checklist de Produção
1.  **Rate Limiting:** Máximo 3 reviews/hora por IP.
2.  **Upload Seguro:** Validação MIME no servidor, fotos fora da pasta `public` (signed URLs).
3.  **Cross-Tenant:** Testes automatizados de isolamento (Lojista A nunca vê dados de B).
4.  **Criptografia:** Contatos de clientes (PII) devem ser salvos via `Model::encrypt()`.
5.  **Cache:** Scripts de bots em Redis para evitar consultas pesadas em cada scan.
6.  **Offline First:** Service Worker deve servir o shell do app mesmo sem rede.

---

## ══ 08 — ROADMAP DE IMPLEMENTAÇÃO (CHECKLIST) ══

Abaixo está o status atual do desenvolvimento do projeto:

- [x] **Sprint 1: Fundação** — Migrations, models (Avaliação, Cliente), seeders e sistema de autenticação (Auth).
- [x] **Sprint 2: API pública + multi-tenancy** — Endpoints `/api/review` e `/api/bot-script` com isolamento por tenant.
- [x] **Sprint 3: PWA conversacional** — Interface de chat PWA com suporte a idioma automático (PT/JA) e modo offline.
- [x] **Sprint 4: Painel Admin** — CRUD de lojistas (clientes) e dashboard de controle administrativo.
- [x] **Sprint 5: Notificações** — Integração com `NotificationService` para WhatsApp e LINE via Evolution API.
- [x] **Sprint 6: Painel Lojista (Core)** — Gestão de avaliações recentes, detalhamento e fechamento do ciclo de feedback.
- [x] **Sprint 7: Dashboard BI** — Implementação inicial de insights (médias, negativas, períodos e clientes novos/recorrentes).
- [x] **Sprint 8: Configurador do bot** — Interface para edição das mensagens de boas-vindas e agradecimento nos dois idiomas.
- [ ] **Sprint 9: Monitoramento & Testes** — Painel ADM-05/06 (Monitor de notificações) e LJT-05/06 (Conta Lojista). Testes de integração em fluxos críticos.
- [ ] **Sprint 10: Finalização & Deploy** — Hardening de segurança, auditoria, índices de banco otimizados e configuração de produção (Hostinger).
- [ ] **v2.0: Futuro (Mês 3+)** — Multi-idioma completo JP, portal self-service, Checkout Stripe/PIX e onboarding automatizado.

---

## ══ 09 — DESIGN SYSTEM (VIBRANT LOGIC) ══

### 1. Visão Geral
Equilíbrio entre a robustez técnica SaaS e a fluidez conversacional. Prioridade em: **Legibilidade, Alto Contraste e Hierarquia Visual.**

### 2. Paleta de Cores (Brand & Interface)
- **Primária (Brand):** Escala `#F5F3FF` (50) até `#4C1D95` (900). 
  - **Destaque (600):** `#7C3AED` (Roxo Vibrant)
- **Feedback:** 
  - Sucesso: `#10B981` | Aviso: `#F59E0B` | Erro: `#EF4444`
- **Neutras:** 
  - Fundo: `#F9FAFB` | Card: `#FFFFFF` | Texto: `#111827`

### 3. Tipografia (Escala IBM Plex)
- **Display:** `Bebas Neue` (Títulos de impacto e Hero).
- **Sans-Serif:** `IBM Plex Sans` (Interface principal e corpo de texto).
- **Monospaced:** `IBM Plex Mono` (Dados técnicos e metadados).

| Escala | Tamanho | Peso |
| :--- | :--- | :--- |
| **Display H1** | 64px | 700 |
| **Título 1** | 32px | 600 |
| **Corpo G** | 16px | 400 |
| **Corpo M** | 14px | 400 |

### 4. Componentes e Acessibilidade
- **Arredondamento:** Padrão 8px (`rounded-lg`).
- **Sombras:** Elevada (`shadow-lg`) para modais, leve (`shadow-sm`) para cards.
- **Contraste:** Ratio mínimo 4.5:1 (WCAG AA) em toda a interface.

---
**CP REVIEW SAAS — Documento Interno de Diretrizes**
Versão 2.2 • Abril 2026 • Design System: Vibrant Logic
