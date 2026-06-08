#!/bin/bash
# deploy.sh — Atualiza arquivos do servidor sem tocar no banco
# Uso: bash deploy.sh
# Pré-requisito: estar na pasta raiz do projeto com git configurado

set -e

BOLD="\033[1m"
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RESET="\033[0m"

echo -e "${BOLD}=== Deploy — Sistema de Cobrança ===${RESET}"

# 1. Git pull
echo -e "\n${YELLOW}[1/6] Atualizando arquivos via git...${RESET}"
git pull origin master

# 2. Composer
echo -e "\n${YELLOW}[2/6] Instalando dependências PHP...${RESET}"
composer install --no-dev --optimize-autoloader --quiet

# 3. Laravel cache clear
echo -e "\n${YELLOW}[3/6] Limpando caches do Laravel...${RESET}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize

# 4. Python packages
echo -e "\n${YELLOW}[4/6] Verificando pacotes Python...${RESET}"
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

# 5. Permissões
echo -e "\n${YELLOW}[5/6] Ajustando permissões...${RESET}"
sudo chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || \
    chmod -R 775 storage bootstrap/cache

# 6. Reinicia PHP-FPM (opcache)
echo -e "\n${YELLOW}[6/6] Reiniciando PHP-FPM...${RESET}"
PHP_FPM_SERVICE=$(systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '/^php[0-9]+\.[0-9]+-fpm\.service/ {print $1}' | sort -V | tail -n 1)

if [ -n "$PHP_FPM_SERVICE" ]; then
    if systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
        sudo systemctl restart "$PHP_FPM_SERVICE"
        echo "    ${PHP_FPM_SERVICE%.service} reiniciado."
    else
        echo "    ${PHP_FPM_SERVICE%.service} está instalado, mas não está ativo — pulando restart."
    fi
else
    echo "    Nenhum serviço phpX.Y-fpm encontrado — pulando."
fi

echo -e "\n${GREEN}${BOLD}Deploy concluído com sucesso!${RESET}"
echo -e "Banco de dados não foi alterado.\n"
