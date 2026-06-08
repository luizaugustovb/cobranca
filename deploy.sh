#!/bin/bash
# deploy.sh — Atualiza arquivos do servidor sem tocar no banco
# Uso: bash deploy.sh
# Pré-requisito: estar na pasta raiz do projeto com git configurado

set -e

BOLD="\033[1m"
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RESET="\033[0m"

APP_USER="${SUDO_USER:-$USER}"
APP_GROUP="www-data"
SUDO="sudo"

if [ "$(id -u)" -eq 0 ] || ! command -v sudo >/dev/null 2>&1; then
    SUDO=""
fi

if ! getent group "$APP_GROUP" >/dev/null 2>&1; then
    APP_GROUP="$(id -gn "$APP_USER")"
fi

echo -e "${BOLD}=== Deploy — Sistema de Cobrança ===${RESET}"

# 1. Git pull
echo -e "\n${YELLOW}[1/7] Atualizando arquivos via git...${RESET}"
git pull origin master

# 2. Prepara diretórios e permissões antes do Composer
echo -e "\n${YELLOW}[2/7] Preparando permissões do Laravel...${RESET}"
mkdir -p storage/logs bootstrap/cache
$SUDO chown -R "$APP_USER":"$APP_GROUP" storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
touch storage/logs/laravel.log
$SUDO chown "$APP_USER":"$APP_GROUP" storage/logs/laravel.log

# 3. Composer
echo -e "\n${YELLOW}[3/7] Instalando dependências PHP...${RESET}"
composer install --no-dev --optimize-autoloader --quiet

# 4. Laravel cache clear
echo -e "\n${YELLOW}[4/7] Limpando caches do Laravel...${RESET}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize

# 5. Python packages
echo -e "\n${YELLOW}[5/7] Verificando pacotes Python...${RESET}"
if [ -d ".venv" ]; then
    source .venv/bin/activate
    pip install pdfplumber tabula-py openpyxl --quiet
    deactivate
    echo "    venv existente atualizado."
else
    echo "    .venv não encontrado — criando..."
    python3 -m venv .venv
    source .venv/bin/activate
    pip install pdfplumber tabula-py openpyxl --quiet
    deactivate
    echo "    venv criado com sucesso."
fi

# 6. Ajuste final de permissões
echo -e "\n${YELLOW}[6/7] Ajustando permissões finais...${RESET}"
$SUDO chown -R "$APP_USER":"$APP_GROUP" storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# 7. Reinicia PHP-FPM (opcache)
echo -e "\n${YELLOW}[7/7] Reiniciando PHP-FPM...${RESET}"
PHP_FPM_SERVICE=$(systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '/^php[0-9]+\.[0-9]+-fpm\.service/ {print $1}' | sort -V | tail -n 1)

if [ -n "$PHP_FPM_SERVICE" ]; then
    if systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
        $SUDO systemctl restart "$PHP_FPM_SERVICE"
        echo "    ${PHP_FPM_SERVICE%.service} reiniciado."
    else
        echo "    ${PHP_FPM_SERVICE%.service} está instalado, mas não está ativo — pulando restart."
    fi
else
    echo "    Nenhum serviço phpX.Y-fpm encontrado — pulando."
fi

echo -e "\n${GREEN}${BOLD}Deploy concluído com sucesso!${RESET}"
echo -e "Banco de dados não foi alterado.\n"
