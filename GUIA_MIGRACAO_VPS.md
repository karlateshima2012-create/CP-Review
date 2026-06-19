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
