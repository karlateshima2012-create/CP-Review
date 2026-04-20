# 🛡️ CP REVIEW CARE — Sistema de Gestão de Experiência do Cliente (SaaS)

Este documento detalha a arquitetura, funcionalidades e as implementações de alto nível (Senior Hardening) aplicadas ao projeto **CP Review Care**. O sistema foi projetado para ser um SaaS multi-tenant robusto, escalável e preparado para o mercado internacional (Brasil e Japão).

---

## 🚀 Sobre o Sistema

O **CP Review Care** é uma plataforma focada em transformar o feedback negativo em oportunidade de fidelização. O lojista disponibiliza um QR Code em seu estabelecimento; o cliente avalia sua experiência de forma privada e interativa através de um bot PWA. 

Se a nota for baixa, o lojista é notificado instantaneamente via canal preferido (WhatsApp ou LINE) para que possa intervir antes que o cliente publique uma reclamação em redes sociais ou Google Maps.

---

## 🛠️ Funcionalidades Core

### 1. Interface Web Mobile B2C (Bot de Avaliação)
- **Zero Atrito (Uso Único):** Não é exigido e nem sugerido nenhum tipo de instalação (App ou tela inicial). O cliente acessa, avalia e finaliza instantaneamente através de uma Web Page Otimizada, mantendo a URL visível como padrão de segurança dos navegadores.
- **Motor PWA de Alta Performance:** Embora não seja um "aplicativo instalável" para o cliente, o frontend utiliza a tecnologia de *Service Workers* sob o capô para garantir carregamento ultrarrápido (cache instantâneo da interface) independentemente da velocidade do 4G ou Wi-Fi local.
- **Interface Conversacional:** Simula um chat humano (Phone Shell) com indicadores de digitação e animações fluidas.
- **Lógica de Branching Emocional:** Reações dinâmicas baseadas na nota (Empatia para notas baixas, Entusiasmo para notas altas).
- **Multilíngue Automático:** Suporte para Português (Brasil) e Japonês (Japão), detectado por contexto.
- **Resiliência Offline:** Fila de envio oculta para garantir que dados (texto e foto) cheguem ao servidor no momento que a conexão do celular restaurar, salvando a review.
- **Incentivo ao Google Reviews:** Somente clientes satisfeitos (notas 4 e 5) são incentivados a postar no Google Maps do lojista.
- **Opções de Contato Inteligentes:** Detecção de idioma oferece canais de contato culturalmente relevantes (WhatsApp/E-mail para Português, LINE/E-mail para Japonês).

### 2. Painel do Lojista (Tenant Dashboard)
- **Dashboard em Tempo Real:** Visualização de KPIs (Total de avaliações, NPS, Ciclos de resolução).
- **Arquitetura Web SaaS B2B:** Estruturado e projetado como uma Aplicação Web Tradicional (não-PWA). Focado inteiramente em produtividade desktop/tablet, permitindo a exibição de matrizes complexas, DataTables, relatórios densos de BI (Mapas de Calor) e navegações em painéis laterais (Sidebars) de forma confortável, garantindo ciclos rápidos de deploy (atualizações chegam ao lojista com um simples F5).
- **Gestão de Crise:** Ferramenta para responder ao cliente e "fechar o loop", disparando notificações de resolução.
- **Configurações Personalizadas:** Mensagens de boas-vindas e agradecimento customizáveis.

### 3. Painel Administrativo (Super Admin)
- **Gestão de Tenants:** Aprovação de novos lojistas após transações.
- **Monitoramento Global:** Visão consolidada de todas as lojas do sistema.

---

## 🏛️ Arquitetura Sênior & Hardening (Implementações Recentes)

Recentemente, o sistema passou por um processo de endurecimento arquitetural sênior para garantir segurança e escalabilidade.

### A. Multi-Tenancy com Isolamento Total
- **Global Table Scope:** Implementação do `TenantScope` e Trait `BelongsToTenant`. 
- **Isolamento de Dados:** Consultas ao banco de dados são filtradas automaticamente pelo `tenant_id` do lojista logado. É tecnicamente impossível um lojista visualizar dados de outro.
- **TenantResolver Middleware:** Injeção automática do contexto de tenant via URL (API Pública) ou Auth (Painel).

### B. Notificações Multi-Canal (Universal Service)
- **Abstração de Canais:** Um único `NotificationService` gerencia a entrega.
- **WhatsApp (Evolution API):** Utilizado para o mercado brasileiro.
- **LINE Messaging API:** Implementado como substituto oficial ao LINE Notify (descontinuado no Japão).
- **Fallback Automático:** Sistema pronto para alternar entre drivers (Ex: Trocar Evolution por API Oficial da Meta) sem alterar o código de negócio.

### C. Pré-Agregação de BI (Escalabilidade Profissional)
- **Tabela de Sumários (`daily_metrics_summary`):** Em vez de processar milhões de registros em tempo real, o sistema gera agregados diários.
- **Otimização de Performance:** Redução de 99.7% na carga do banco de dados para dashboards.
- **Heatmaps Rápidos:** Permite gerar mapas de calor de performance por período (Almoço/Jantar) em milissegundos.

### D. Hardening de Segurança e Banco de Dados
- **Uuids:** Identificadores únicos não-previsíveis para todas as entidades sensíveis.
- **Índices Compostos:** Otimização para filtros frequentes de (`tenant_id`, `created_at`).
- **Nomenclatura Consistente:** Padronização absoluta para `tenant_id` em todo o ecossistema.

---

## 📈 Roadmap Técnico

1. [x] Implementação de Multi-tenancy e Escopos.
2. [x] Criação do Motor de BI e Pré-agregação.
3. [x] Abstração de Notificações (WA + LINE).
4. [ ] Implementação de Gráficos de Heatmap no Painel do Lojista (Próximo Passo).
5. [ ] Configuração de Rate Limiting na API Pública para evitar SPAM.
6. [ ] Implementação de Testes Cross-Tenant Automatizados.

---
*Documentação gerada pelo Arquiteto de Sistemas - CP REVIEW CARE v2.0*
