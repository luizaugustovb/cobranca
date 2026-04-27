# Guia de Deploy — Sistema de Cobrança

> **Premissa:** servidor Ubuntu/Debian em EC2 AWS (ou qualquer VPS Linux).  
> O banco de dados **não é alterado** em nenhuma etapa abaixo — apenas arquivos.

---

## 1. Requisitos do servidor

| Pacote | Versão mínima |
|--------|--------------|
| PHP    | 8.2          |
| Composer | 2.x       |
| Node.js | 18.x (só para rebuild de assets) |
| Python | 3.10+        |
| Java (JRE) | 11+ (exigido pelo tabula-py) |
| Git    | qualquer     |

---

## 2. Primeiro deploy (servidor zerado)

### 2.1 Pacotes do sistema

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2 + extensões
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-sqlite3

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Python e venv
sudo apt install -y python3 python3-pip python3-venv

# Java (exigido pelo tabula-py)
sudo apt install -y default-jre

# Git e Nginx
sudo apt install -y git nginx
```

### 2.2 Clonar o repositório

```bash
cd /var/www
sudo git clone https://github.com/luizaugustovb/cobranca cobranca
sudo chown -R $USER:$USER /var/www/cobranca
cd /var/www/cobranca
```

### 2.3 Dependências PHP

```bash
composer install --no-dev --optimize-autoloader
```

### 2.4 Ambiente (.env)

```bash
cp .env.example .env
php artisan key:generate
```

Edite `.env` com as configurações do servidor:

```bash
nano .env
```

Campos obrigatórios:
```
APP_URL=https://seudominio.com
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=sqlite
# ou configure MySQL/PostgreSQL se aplicável

MAIL_MAILER=smtp
MAIL_HOST=...
```

### 2.5 Banco de dados (somente no primeiro deploy)

```bash
php artisan migrate --force
php artisan db:seed --force   # apenas se quiser dados iniciais
```

### 2.6 Permissões

```bash
sudo chown -R www-data:www-data /var/www/cobranca/storage
sudo chown -R www-data:www-data /var/www/cobranca/bootstrap/cache
sudo chmod -R 775 /var/www/cobranca/storage
sudo chmod -R 775 /var/www/cobranca/bootstrap/cache
```

### 2.7 Python — venv e pacotes

```bash
cd /var/www/cobranca
python3 -m venv .venv
source .venv/bin/activate
pip install pdfplumber tabula-py openpyxl
deactivate
```

Teste rápido:
```bash
.venv/bin/python3 scripts/pdf_extractor.py --help
```

### 2.8 Assets (CSS/JS)

Se o servidor tiver Node.js instalado:
```bash
npm ci
npm run build
```

Se não tiver Node.js, faça o build localmente e suba a pasta `public/build/` via rsync ou scp:
```bash
# Rode localmente e envie para o servidor
rsync -avz public/build/ usuario@IP:/var/www/cobranca/public/build/
```

### 2.9 Nginx

Crie o arquivo de configuração:
```bash
sudo nano /etc/nginx/sites-available/cobranca
```

Conteúdo:
```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/cobranca/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/cobranca /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 2.10 HTTPS com Certbot (opcional)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d seudominio.com -d www.seudominio.com
```

---

## 3. Atualização (sem mexer no banco)

Use o script `deploy.sh` disponível na raiz do projeto:

```bash
cd /var/www/cobranca
bash deploy.sh
```

Ou manualmente:

```bash
cd /var/www/cobranca

git pull origin master

composer install --no-dev --optimize-autoloader

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize

# Se mudou o venv ou pacotes Python
source .venv/bin/activate
pip install pdfplumber tabula-py openpyxl --quiet
deactivate

# Se mudou CSS/JS (com Node no servidor)
npm ci && npm run build

# Ajusta permissões
sudo chown -R www-data:www-data storage bootstrap/cache

# Reinicia PHP-FPM para limpar opcache
sudo systemctl restart php8.2-fpm
```

> **NÃO rode** `php artisan migrate` em atualizações normais.

---

## 4. Script deploy.sh

O arquivo `deploy.sh` na raiz do projeto automatiza a etapa 3:

```bash
bash deploy.sh
```

---

## 5. Arquivos que NUNCA devem ser sobrescritos

| Arquivo/Pasta | Motivo |
|---------------|--------|
| `.env` | Configurações do servidor (tokens, senhas) |
| `database/database.sqlite` | Banco de dados SQLite |
| `storage/` | Uploads, logs, cache de sessão |
| `.venv/` | Virtualenv Python (gerado localmente no servidor) |

Todos estão no `.gitignore` — o `git pull` não os afeta.

---

## 6. Verificação pós-deploy

```bash
# Testa resposta HTTP
curl -I http://localhost

# Verifica log de erros recentes
tail -50 storage/logs/laravel.log

# Testa extração Python
.venv/bin/python3 scripts/pdf_extractor.py --help
```
