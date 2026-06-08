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

### 2.0 Instalação automática (recomendado)

Após clonar o repositório, execute:

```bash
cd /var/www/cobranca
bash instalar.sh
```

O script `instalar.sh` automatiza:
- `apt update` e `apt upgrade`
- instalação de dependências base (git, nginx, python, java etc.)
- configuração do repositório do PHP (Ubuntu/Debian)
- instalação do PHP e extensões necessárias
- instalação do Composer
- instalação do Node.js (quando necessário)
- `composer install`, `npm run build`, criação/atualização da `.venv`
- ajustes de permissões e reinício de serviços

No setup atual, as pastas `storage/` e `bootstrap/cache` ficam com usuário de deploy e grupo web (`www-data`) para evitar erro de escrita no `artisan package:discover`.

> Mesmo com automação, revise o `.env` e a configuração final do Nginx (`server_name`, SSL, domínio).

### 2.1 Pacotes do sistema

```bash
sudo apt update && sudo apt upgrade -y

# Dependências básicas
sudo apt install -y software-properties-common ca-certificates lsb-release apt-transport-https curl gnupg

# Repositório PHP (Ubuntu: Ondrej, Debian: Sury)
if [ -f /etc/os-release ]; then
    . /etc/os-release
    if [ "$ID" = "ubuntu" ]; then
        sudo add-apt-repository -y ppa:ondrej/php
    elif [ "$ID" = "debian" ]; then
        curl -fsSL https://packages.sury.org/php/apt.gpg | sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-php.gpg
        echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
    fi
fi

sudo apt update

# PHP + extensões (tenta 8.2; se não existir, usa 8.3 ou 8.1)
PHPV="8.2"
if ! apt-cache show php${PHPV}-fpm >/dev/null 2>&1; then
    if apt-cache show php8.3-fpm >/dev/null 2>&1; then
        PHPV="8.3"
    elif apt-cache show php8.1-fpm >/dev/null 2>&1; then
        PHPV="8.1"
    else
        echo "Nenhuma versão php8.x (fpm) encontrada nos repositórios." >&2
        exit 1
    fi
fi

echo "Instalando PHP ${PHPV}..."
sudo apt install -y php${PHPV} php${PHPV}-cli php${PHPV}-fpm php${PHPV}-mbstring \
        php${PHPV}-xml php${PHPV}-curl php${PHPV}-zip php${PHPV}-gd php${PHPV}-sqlite3

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
cd /var/www/cobranca

# Ajuste USER_DEPLOY para o usuário que executa o deploy (ex.: cobrancapro, ubuntu, deploy)
USER_DEPLOY="cobrancapro"

sudo mkdir -p storage/logs bootstrap/cache
sudo chown -R ${USER_DEPLOY}:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
sudo touch storage/logs/laravel.log
sudo chown ${USER_DEPLOY}:www-data storage/logs/laravel.log
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
        # Ajuste a versão conforme o PHP instalado (ex.: 8.3, 8.2, 8.1)
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
USER_DEPLOY="cobrancapro"
sudo chown -R ${USER_DEPLOY}:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
sudo touch storage/logs/laravel.log
sudo chown ${USER_DEPLOY}:www-data storage/logs/laravel.log

# Reinicia PHP-FPM para limpar opcache
sudo systemctl restart php8.2-fpm  # ou php8.3-fpm / php8.1-fpm
```

> **NÃO rode** `php artisan migrate` em atualizações normais.

---

## 4. Script deploy.sh

O arquivo `deploy.sh` na raiz do projeto automatiza a etapa 3:

```bash
bash deploy.sh
```

O script já prepara permissões **antes** do `composer install`, evitando o erro:

```text
The stream or file "storage/logs/laravel.log" could not be opened in append mode
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
