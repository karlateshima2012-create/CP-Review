# 🚢 Guia de Deploy — CP REVIEW CARE

Este guia documenta as duas formas de realizar o deploy do projeto **CP REVIEW** para o servidor VPS da **Hostinger**: 

1. **Deploy Automatizado (CI/CD)** via **GitHub Actions** usando chaves SSH.
2. **Deploy Local Rápido** via script local `deploy.sh` usando `rsync`.

---

## 🏗️ 1. Deploy via GitHub Actions

O deploy é disparado automaticamente a cada **`push`** ou **`merge`** na branch **`main`**.

### Fluxo do Pipeline:
1.  **Build Local (GitHub):** O GitHub prepara a máquina virtual com PHP 8.4 e Node.js 24.
2.  **Dependências:** Instala dependências do Composer (`--no-dev`) e do NPM.
3.  **Vite Build:** Compila os ativos de frontend (CSS/JS) e gera a pasta `public/build`.
4.  **Transferência (SCP):** Transfere os arquivos compilados e códigos de forma segura para a VPS.
5.  **Comandos Remotos (SSH):**
    *   Executa as migrações no banco de dados (`php artisan migrate --force`).
    *   Limpa e gera os caches de config, rotas e views.
    *   Ajusta as permissões de pastas (`chown -R www-data:www-data storage bootstrap/cache`).

### 🔑 Configuração de Secrets no GitHub
Para que o deploy funcione, o repositório no GitHub deve ter as seguintes **Secrets** configuradas em *Settings -> Secrets and variables -> Actions*:

| Nome | Descrição | Valor Configurado |
| :--- | :--- | :--- |
| `SSH_HOST` | IP público da sua VPS | `76.13.209.192` |
| `SSH_USER` | Usuário de acesso SSH | `root` |
| `SSH_PORT` | Porta do SSH da VPS | `22` |
| `PROD_PATH` | Pasta de instalação do projeto | `/var/www/cpreview` |
| `SSH_PRIVATE_KEY` | Conteúdo da chave privada Ed25519 | *Chave SSH criada em `./github_actions_key`* |

---

## 💻 2. Deploy Rápido Local (`deploy.sh`)

Muitas vezes o firewall da Hostinger bloqueia requisições vindas dos IPs dinâmicos do GitHub Actions, gerando timeouts de conexão. Para contornar isso, configurei um script local de deploy direto da sua máquina que ignora esse bloqueio.

### Como usar:
1.  No seu terminal local, execute:
    ```bash
    ./deploy.sh
    ```
2.  O script fará de forma automatizada:
    *   Compilação local dos assets (Vite).
    *   Sincronização incremental inteligente dos arquivos via `rsync` (transferindo apenas arquivos modificados e ignorando logs, temporários e `node_modules`).
    *   Acesso SSH seguro usando a chave privada local `./github_actions_key`.
    *   Limpeza e regeneração de caches de produção.
    *   Execução de migrações (`migrate --force`) e correção de permissões no servidor.

---

## ⚠️ Cuidados e Boas Práticas

### Alterações no `.env`
O deploy automático não gerencia o seu arquivo `.env` de produção (que já está criado e configurado na VPS). Se você adicionar uma nova variável de ambiente (como credenciais de APIs ou chaves de webhook):
1.  Acesse o servidor via SSH: `ssh -i ./github_actions_key root@76.13.209.192`
2.  Edite o arquivo manualmente: `nano /var/www/cpreview/.env`
3.  Execute a limpeza de cache: `php artisan config:cache` no diretório do projeto.

### Migrações de Banco de Dados
A execução de migrações com o comando `--force` é direta e não solicita confirmação. Caso vá realizar alterações destrutivas (excluir tabelas ou colunas), certifique-se de realizar um backup (dump) do banco MySQL da VPS previamente.

---
*Atualizado por Antigravity AI - Protocolo de Deployment v1.1*
