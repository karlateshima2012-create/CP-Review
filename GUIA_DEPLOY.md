# 🚢 Guia de Deploy — CP REVIEW CARE

Este guia documenta o fluxo de CI/CD (Integração e Entrega Contínua) utilizando **GitHub Actions**. O sistema foi configurado para que cada alteração aprovada seja enviada ao servidor de produção de forma segura e automatizada.

---

## 🏗️ Fluxo de Deploy

O deploy é acionado automaticamente em toda **`push`** ou **`merge`** na branch **`main`**.

1.  **Build Local (GitHub):** O GitHub prepara o ambiente com PHP 8.4 e Node.js 20.
2.  **Dependências:** Instala dependências do Composer (`--no-dev`) e do NPM.
3.  **Vite Build:** Compila os ativos de frontend (CSS/JS).
4.  **Transferência (SCP):** Os arquivos são transferidos via SCP para o servidor.
5.  **Comandos Remotos (SSH):**
    *   Executa as migrações (`artisan migrate --force`).
    *   Limpa e gera os caches de config, rotas e views.
    *   Ajusta permissões de pastas.

---

## 🔑 Configuração de Secrets

Para que o deploy funcione, o repositório no GitHub deve ter as seguintes **Secrets** configuradas em *Settings -> Secrets and variables -> Actions*:

| Nome | Descrição | Exemplo |
| :--- | :--- | :--- |
| `SSH_HOST` | IP ou Hostname do Servidor | `cpreview.creativeprintjp.com` |
| `SSH_USER` | Usuário de acesso SSH | `admin_user` |
| `SSH_PASSWORD` | Senha do usuário SSH | `********` |
| `SSH_PORT` | Porta SSH | `22` |
| `PROD_PATH` | Caminho absoluto da pasta do projeto | `/domains/creativeprintjp.com/...` |

---

## 🚀 Como Disparar o Deploy Corretamente

Para garantir um deploy sem interrupções e com segurança, siga este workflow:

1.  **Desenvolvimento Local:** Faça suas alterações em branches separadas ou diretamente na `main` (se for o único desenvolvedor).
2.  **Validação de Banco:** Antes de subir, certifique-se de que as migrações não têm conflitos. O deploy executará `migrate --force` automaticamente.
3.  **Commit:** Tente agrupar alterações lógicas em um único commit.
    ```bash
    git add .
    git commit -m "feat: implementacao de bi pre-agregado"
    ```
4.  **Push:** Envie para a branch principal.
    ```bash
    git push origin main
    ```
5.  **Monitoramento:**
    *   Vá até a aba **"Actions"** no seu repositório no GitHub.
    *   Acompanhe o workflow "Deploy CP Review to Production".
    *   Se algum passo ficar vermelho (erro), verifique os logs clicando no job.

---

## ⚠️ Cuidados Importantes

### Mudanças no `.env`
O GitHub Actions não gerencia o seu arquivo `.env` de produção. Se você adicionar uma nova chave (ex: `LINE_CHANNEL_TOKEN`), você deve:
1.  Acessar o servidor via SSH ou FTP.
2.  Editar o arquivo `.env` manualmente na pasta do projeto.
3.  Rodar `php artisan config:cache` no servidor (ou aguardar o próximo deploy).

### Conflitos de Permissão
Se o deploy falhar no passo de SCP, verifique se o usuário SSH tem permissão de escrita na pasta de destino (`domains/creativeprintjp.com/public_html/cpreview`).

### Migrações Destrutivas
O comando `--force` no migrate não pede confirmação. Tenha cuidado ao excluir colunas que contenham dados importantes. Sempre faça backup do banco antes de mudanças drásticas de schema.

---
*Gerado por Antigravity AI - Protocolo de Deployment v1.0*
