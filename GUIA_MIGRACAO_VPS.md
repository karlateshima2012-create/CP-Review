# 🚀 Guia de Migração para VPS Hostinger — CP REVIEW

Este guia detalha o passo a passo completo para migrar, configurar e implantar o projeto **CP REVIEW** em uma VPS da **Hostinger** (utilizando Ubuntu 22.04 LTS e Nginx como servidor web).

---

## 🛠️ Passo 1: Preparação do Sistema Operacional (SSH)

Acesse sua VPS via terminal utilizando suas credenciais de administrador (root):
```bash
ssh root@IP_DA_SUA_VPS
```

Atualize os pacotes do sistema:
```bash
sudo apt update && sudo apt upgrade -y
```

---

## 📦 Passo 2: Instalação do PHP 8.4 e Extensões

Adicione o repositório oficial do PHP (Ondrej Sury):
```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

Instale o PHP 8.4 e as dependências essenciais requeridas pelo Laravel:
```bash
sudo apt install php8.4-fpm php8.4-cli php8.4-mysql php8.4-curl php8.4-gd php8.4-mbstring php8.4-xml php8.4-zip php8.4-bcmath php8.4-intl php8.4-sqlite3 php8.4-common -y
```

Verifique a instalação:
```bash
php -v
```

---

## 🛢️ Passo 3: Configuração do Banco de Dados (MySQL)

Instale o servidor MySQL:
```bash
sudo apt install mysql-server -y
```

Acesse o console do MySQL:
```bash
sudo mysql
```

Crie o banco de dados e o usuário da aplicação (substitua `SuaSenhaSegura` por uma senha forte):
```sql
CREATE DATABASE u176367625_cpreview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cpreview_user'@'localhost' IDENTIFIED BY 'SuaSenhaSegura';
GRANT ALL PRIVILEGES ON u176367625_cpreview.* TO 'cpreview_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 🚚 Passo 4: Instalar Composer e Clonar o Projeto

Instale o Composer (Gerenciador de Dependências PHP):
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

Crie o diretório do projeto e ajuste o proprietário inicial:
```bash
sudo mkdir -p /var/www/cpreview
sudo chown -R $USER:$USER /var/www/cpreview
```

Clone o repositório diretamente no servidor (ou configure chaves SSH para clonagem do repositório privado se necessário):
```bash
git clone https://github.com/karlateshima2012-create/CP-Review.git /var/www/cpreview
```

Entre no diretório e instale as dependências:
```bash
cd /var/www/cpreview
composer install --no-dev --optimize-autoloader
```

---

## ⚙️ Passo 5: Arquivo de Configuração (.env) e Artisan

Crie o arquivo `.env` a partir do modelo padrão:
```bash
cp .env.example .env
```

Edite o arquivo `.env` para ajustar os dados do seu servidor (use o editor `nano` ou `vim`):
```bash
nano .env
```

**Principais campos a ajustar:**
```ini
APP_NAME="CP Review"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cpreview.creativeprintjp.com  # Insira seu domínio definitivo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u176367625_cpreview
DB_USERNAME=cpreview_user
DB_PASSWORD=SuaSenhaSegura # Senha configurada no MySQL

# Configure também seus dados de envio de e-mail (Mailgun/SMTP) e tokens da API do LINE se houver.
```

Salve e feche o editor (`Ctrl + O`, depois `Enter`, depois `Ctrl + X`).

Gere a chave única da aplicação:
```bash
php artisan key:generate
```

Execute as migrações para estruturar as tabelas do banco de dados:
```bash
php artisan migrate --force
```

Gere o link simbólico para que as imagens carregadas fiquem públicas:
```bash
php artisan storage:link
```

---

## 🌐 Passo 6: Servidor Web (Nginx) e SSL

Instale o Nginx:
```bash
sudo apt install nginx -y
```

Crie o arquivo de configuração para o domínio do CP Review:
```bash
sudo nano /etc/nginx/sites-available/cpreview
```

Cole a seguinte configuração (certifique-se de alterar `cpreview.creativeprintjp.com` para o seu domínio):
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name cpreview.creativeprintjp.com;
    root /var/www/cpreview/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Habilite o site e teste a sintaxe do Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cpreview /etc/nginx/sites-enabled/
sudo nginx -t
```

Se o teste passar com sucesso, reinicie o Nginx:
```bash
sudo systemctl restart nginx
```

### Instalar Certificado SSL Gratuito (Let's Encrypt / Certbot)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d cpreview.creativeprintjp.com
```
*(Siga as instruções na tela e escolha a opção para redirecionar todo o tráfego HTTP para HTTPS automaticamente)*.

---

## 🔒 Passo 7: Permissões de Pastas e Segurança

Para que o Laravel consiga criar logs e armazenar arquivos de logotipo e capas recebidas no painel, ajuste as permissões para o usuário padrão do servidor web (`www-data`):
```bash
sudo chown -R www-data:www-data /var/www/cpreview/storage
sudo chown -R www-data:www-data /var/www/cpreview/bootstrap/cache
sudo chmod -R 775 /var/www/cpreview/storage
sudo chmod -R 775 /var/www/cpreview/bootstrap/cache
```

---

## 📅 Passo 8: Agendador do Laravel (Task Scheduling)

Muitos recursos, como NPS e envio de relatórios automáticos mensais, dependem do cron. Insira o agendador na cron da máquina:
```bash
crontab -e
```

Adicione a seguinte linha no final do arquivo:
```cron
* * * * * cd /var/www/cpreview && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🚢 Passo 9: Configurando a Integração Contínua (CI/CD GitHub Actions)

Com a VPS configurada, configure as **Secrets** no repositório do seu GitHub (*Settings -> Secrets and variables -> Actions*):

1. `SSH_HOST`: IP público da sua VPS Hostinger.
2. `SSH_USER`: Usuário SSH que tem acesso de escrita na pasta `/var/www/cpreview` (você pode usar um usuário dedicado ou o `root`, mas o usuário dedicado com permissões é mais recomendado).
3. `SSH_PASSWORD`: Senha do usuário configurado.
4. `SSH_PORT`: `22` (ou a porta SSH configurada).
5. `PROD_PATH`: `/var/www/cpreview` (caminho onde o projeto está hospedado).

Ao fazer qualquer `git push` para a branch `main`, as dependências serão compiladas via GitHub Actions e transferidas via SCP automaticamente para a VPS, rodando migrações e otimizando os caches de rotas e views de forma transparente!

---

## 💾 Passo 10: Backups Diários no Google Drive (rclone)

O sistema realiza backups automáticos diários às **02:00** (horário da VPS), salvando:
- **Banco de dados** completo (`mysqldump` comprimido com gzip)
- **Arquivos de mídia** (logos e capas dos clientes — `storage/app/public`)

Os backups ficam na pasta `cp-review-backups` do seu Google Drive e são mantidos por **30 dias**.

---

### 10.1 — Criar o Projeto no Google Cloud Console

1. Acesse [console.cloud.google.com](https://console.cloud.google.com)
2. Clique em **Select a project → New Project**
3. Nome: `cp-review-backup` → **Create**
4. Com o projeto selecionado, vá em **APIs & Services → Library**
5. Busque **Google Drive API** → clique → **Enable**

---

### 10.2 — Criar Service Account (credencial do servidor)

1. Em **APIs & Services → Credentials → Create Credentials → Service Account**
2. Preencha:
   - **Name**: `cp-review-backup-sa`
   - **ID**: gerado automaticamente
3. Clique em **Done** (sem precisar atribuir roles)
4. Na lista de Service Accounts, clique no email gerado (ex: `cp-review-backup-sa@cp-review-backup.iam.gserviceaccount.com`)
5. Aba **Keys → Add Key → Create new key → JSON → Create**
6. O arquivo `*.json` será baixado automaticamente — **guarde-o com segurança**

---

### 10.3 — Criar a Pasta de Backups no Google Drive

1. Acesse [drive.google.com](https://drive.google.com)
2. Crie uma nova pasta: `cp-review-backups`
3. Clique com o botão direito na pasta → **Share**
4. No campo de e-mail, cole o e-mail do Service Account (ex: `cp-review-backup-sa@...iam.gserviceaccount.com`)
5. Permissão: **Editor** → **Send**
6. Abra a pasta e copie o **ID** da URL:
   ```
   https://drive.google.com/drive/folders/1ABC123XYZ...
                                           ^^^^^^^^^^^^
                                           Este é o ROOT_FOLDER_ID
   ```

---

### 10.4 — Instalar e Configurar o rclone na VPS

```bash
# Conectar na VPS
ssh root@IP_DA_VPS

# Instalar rclone
curl -fsSL https://rclone.org/install.sh | bash

# Criar pasta de configuração
mkdir -p /root/.config/rclone
mkdir -p /etc/rclone
```

Copie o arquivo JSON do Service Account para a VPS (execute **no seu computador local**):
```bash
scp gdrive-sa.json root@IP_DA_VPS:/etc/rclone/gdrive-sa.json
```

Proteja o arquivo na VPS:
```bash
chmod 600 /etc/rclone/gdrive-sa.json
```

Crie o arquivo de configuração do rclone **na VPS** (use `nano` ou `vim`):
```bash
nano /root/.config/rclone/rclone.conf
```

Cole o conteúdo abaixo, substituindo `ROOT_FOLDER_ID` pelo ID copiado no passo 10.3:
```ini
[gdrive]
type = drive
scope = drive
service_account_file = /etc/rclone/gdrive-sa.json
root_folder_id = ROOT_FOLDER_ID
```

---

### 10.5 — Testar a Conexão e o Backup

```bash
# Listar o conteúdo da pasta (deve aparecer vazia inicialmente)
rclone lsd gdrive:

# Executar o backup manualmente para testar
/var/www/cpreview/backup.sh
```

Se tudo correr bem, você verá os arquivos `db_YYYY-MM-DD_HH-MM.sql.gz` e `files_YYYY-MM-DD_HH-MM.tar.gz` na pasta do Google Drive.

---

### 10.6 — Verificar o Cron Registrado

O cron é registrado automaticamente em todo deploy. Para verificar:
```bash
crontab -l
# Deve conter:
# 0 2 * * * /var/www/cpreview/backup.sh >> /var/log/cpreview-backup.log 2>&1
```

Para acompanhar os logs:
```bash
tail -f /var/log/cpreview-backup.log
```

---

### 10.7 — Restaurar um Backup (em caso de emergência)

```bash
# Baixar o backup mais recente do Drive
rclone copy gdrive:cp-review-backups/ /tmp/restore/ --include "db_*.sql.gz"

# Descomprimir
gunzip /tmp/restore/db_YYYY-MM-DD_HH-MM.sql.gz

# Restaurar o banco
mysql -u cpreview_user -p u176367625_cpreview < /tmp/restore/db_YYYY-MM-DD_HH-MM.sql
```

Para restaurar arquivos de mídia:
```bash
rclone copy gdrive:cp-review-backups/ /tmp/restore/ --include "files_*.tar.gz"
tar -xzf /tmp/restore/files_YYYY-MM-DD_HH-MM.tar.gz -C /var/www/cpreview/storage/app/
```
