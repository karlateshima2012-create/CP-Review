# Especificação do Design System: Vibrant Logic

## 1. Visão Geral
O **Vibrant Logic** foi concebido para equilibrar a robustez técnica de uma plataforma SaaS com a acessibilidade e fluidez de uma interface conversacional. O sistema prioriza legibilidade, alto contraste e uma hierarquia visual clara.

---

## 2. Paleta de Cores

### Cores Primárias (Identidade da Marca)
Utilizadas para elementos estruturais, botões principais e destaques de navegação.
- **Brand 50:** `#F5F3FF`
- **Brand 100:** `#EDE9FE`
- **Brand 200:** `#DDD6FE`
- **Brand 300:** `#C4B5FD`
- **Brand 400:** `#A78BFA`
- **Brand 500:** `#8B5CF6`
- **Brand 600 (Principal):** `#7C3AED`
- **Brand 700:** `#6D28D9`
- **Brand 800:** `#5B21B6`
- **Brand 900:** `#4C1D95`

### Cores de Feedback (Semânticas)
- **Sucesso (Verde):** `#10B981` (Base) | `#059669` (Dark)
- **Aviso (Amarelo):** `#F59E0B` (Base) | `#D97706` (Dark)
- **Erro (Vermelho):** `#EF4444` (Base) | `#DC2626` (Dark)
- **Informação (Azul):** `#3B82F6` (Base) | `#2563EB` (Dark)

### Cores Neutras (Interface)
- **Background Página:** `#F9FAFB`
- **Background Card:** `#FFFFFF`
- **Texto Primário:** `#111827`
- **Texto Secundário:** `#4B5563`
- **Bordas:** `#E5E7EB`

---

## 3. Tipografia

### Famílias de Fontes
- **Display:** `Bebas Neue` (Títulos de Seção, Hero, Display H1)
- **Sans-Serif:** `IBM Plex Sans` (Corpo de texto, formulários, botões)
- **Monospaced:** `IBM Plex Mono` (Dados técnicos, metadados, badges)

### Escala de Tamanhos
| Nível | Tamanho | Peso | Uso |
| :--- | :--- | :--- | :--- |
| **Display H1** | 64px | 700 | Títulos de seção grandes |
| **Título 1** | 32px | 600 | Cabeçalhos de página |
| **Título 2** | 24px | 600 | Títulos de seções internas |
| **Título 3** | 20px | 600 | Cards e subtítulos |
| **Corpo G** | 16px | 400 | Texto principal (Acessibilidade) |
| **Corpo M** | 14px | 400 | Tabelas e metadados |
| **Legenda** | 11px | 500 | Badges e status |

---

## 4. Diretrizes de Acessibilidade
- **Contraste:** Todas as combinações de texto e fundo devem manter um ratio mínimo de 4.5:1 (WCAG AA).
- **Interação:** Estados de focus devem ser claramente visíveis com anéis de cor `--brand-400`.
- **Tipografia:** Tamanho mínimo de 14px para textos padrão e 16px para descrições longas.

---

## 5. Componentes Estruturais
- **Arredondamento:** Base de 8px (`rounded-lg`).
- **Sombras:** `shadow-sm` para cards e `shadow-lg` para modais.
- **Espaçamento:** Escala baseada em 4px (4, 8, 12, 16, 24, 32, 48, 64).
