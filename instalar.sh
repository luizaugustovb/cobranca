#!/usr/bin/env bash

set -Eeuo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export DEBIAN_FRONTEND=noninteractive
APP_USER="${SUDO_USER:-$USER}"
APP_GROUP="www-data"

if [[ "${EUID:-$(id -u)}" -eq 0 ]]; then
  SUDO=""
else
  SUDO="sudo"
  if ! command -v sudo >/dev/null 2>&1; then
    echo "Erro: execute como root ou instale o sudo." >&2
    exit 1
  fi
fi

log() {
  printf "\n\033[1;33m[instalar]\033[0m %s\n" "$1"
}

detect_os() {
  if [[ ! -f /etc/os-release ]]; then
    echo "Erro: não foi possível detectar o sistema operacional." >&2
    exit 1
  fi

  # shellcheck disable=SC1091
  . /etc/os-release
  OS_ID="${ID:-}"
  OS_CODENAME="${VERSION_CODENAME:-}"

  if [[ -z "$OS_ID" || -z "$OS_CODENAME" ]]; then
    echo "Erro: /etc/os-release incompleto." >&2
    exit 1
  fi

  if [[ "$OS_ID" != "ubuntu" && "$OS_ID" != "debian" ]]; then
    echo "Erro: sistema não suportado ($OS_ID). Use Ubuntu ou Debian." >&2
    exit 1
  fi

  if ! getent group "$APP_GROUP" >/dev/null 2>&1; then
    APP_GROUP="$(id -gn "$APP_USER")"
  fi
}

apt_update_upgrade() {
  log "Atualizando pacotes do sistema"
  $SUDO apt update
  $SUDO apt upgrade -y
}

install_base_packages() {
  log "Instalando pacotes base"
  $SUDO apt install -y \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    curl \
    gnupg \
    unzip \
    git \
    nginx \
    python3 \
    python3-pip \
    python3-venv \
    default-jre
}

configure_php_repository() {
  log "Configurando repositório do PHP"

  if [[ "$OS_ID" == "ubuntu" ]]; then
    $SUDO add-apt-repository -y ppa:ondrej/php
  else
    if [[ ! -f /etc/apt/trusted.gpg.d/sury-php.gpg ]]; then
      curl -fsSL https://packages.sury.org/php/apt.gpg | $SUDO gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-php.gpg
    fi
    echo "deb https://packages.sury.org/php/ ${OS_CODENAME} main" | $SUDO tee /etc/apt/sources.list.d/sury-php.list >/dev/null
  fi

  $SUDO apt update
}

pick_php_version() {
  local versions=("8.2" "8.3" "8.1")

  for version in "${versions[@]}"; do
    if apt-cache show "php${version}-fpm" >/dev/null 2>&1; then
      PHP_VERSION="$version"
      return
    fi
  done

  echo "Erro: nenhuma versão php8.x-fpm foi encontrada nos repositórios." >&2
  exit 1
}

install_php_stack() {
  pick_php_version
  log "Instalando PHP ${PHP_VERSION} e extensões"

  $SUDO apt install -y \
    "php${PHP_VERSION}" \
    "php${PHP_VERSION}-cli" \
    "php${PHP_VERSION}-fpm" \
    "php${PHP_VERSION}-mbstring" \
    "php${PHP_VERSION}-xml" \
    "php${PHP_VERSION}-curl" \
    "php${PHP_VERSION}-zip" \
    "php${PHP_VERSION}-gd" \
    "php${PHP_VERSION}-sqlite3" \
    "php${PHP_VERSION}-bcmath" \
    "php${PHP_VERSION}-intl"

  PHP_BIN="php"
}

install_composer() {
  log "Instalando Composer"

  if command -v composer >/dev/null 2>&1; then
    return
  fi

  local installer="/tmp/composer-setup.php"
  curl -sS https://getcomposer.org/installer -o "$installer"
  $SUDO php "$installer" --install-dir=/usr/local/bin --filename=composer --quiet
  rm -f "$installer"
}

install_node_if_needed() {
  log "Verificando Node.js"

  local install_node="false"

  if ! command -v node >/dev/null 2>&1; then
    install_node="true"
  else
    local major
    major="$(node -v | sed -E 's/^v([0-9]+).*/\1/')"
    if [[ -z "$major" || "$major" -lt 18 ]]; then
      install_node="true"
    fi
  fi

  if [[ "$install_node" == "true" ]]; then
    curl -fsSL https://deb.nodesource.com/setup_18.x | $SUDO -E bash -
    $SUDO apt install -y nodejs
  fi
}

prepare_application() {
  log "Preparando aplicação"
  cd "$PROJECT_DIR"

  composer install --no-dev --optimize-autoloader

  if [[ ! -f .env && -f .env.example ]]; then
    cp .env.example .env
    php artisan key:generate
  fi

  if [[ -d .venv ]]; then
    # shellcheck disable=SC1091
    source .venv/bin/activate
  else
    python3 -m venv .venv
    # shellcheck disable=SC1091
    source .venv/bin/activate
  fi

  pip install --upgrade pip
  pip install pdfplumber tabula-py openpyxl
  deactivate

  if [[ -f package.json ]]; then
    if [[ -f package-lock.json ]]; then
      npm ci
    else
      npm install
    fi
    npm run build
  fi

  php artisan config:clear || true
  php artisan cache:clear || true
  php artisan view:clear || true
  php artisan route:clear || true
  php artisan optimize || true

  if [[ -d storage && -d bootstrap/cache ]]; then
    $SUDO chown -R "$APP_USER":"$APP_GROUP" storage bootstrap/cache || true
    find storage bootstrap/cache -type d -exec chmod 775 {} \; || true
    find storage bootstrap/cache -type f -exec chmod 664 {} \; || true
  fi

  if [[ -f .env ]]; then
    $SUDO chown "$APP_USER":"$APP_GROUP" .env || true
    chmod 664 .env || true
  fi

  if [[ -f database/database.sqlite ]]; then
    $SUDO chown "$APP_USER":"$APP_GROUP" database database/database.sqlite || true
    chmod 775 database || true
    chmod 664 database/database.sqlite || true
  fi
}

restart_services() {
  log "Reiniciando serviços"

  $SUDO systemctl enable nginx >/dev/null 2>&1 || true
  $SUDO systemctl restart nginx || true

  local fpm_service
  fpm_service="$($SUDO systemctl list-unit-files --type=service --no-legend 2>/dev/null | awk '/^php[0-9]+\.[0-9]+-fpm\.service/ {print $1}' | sort -V | tail -n 1)"

  if [[ -n "$fpm_service" ]]; then
    $SUDO systemctl enable "$fpm_service" >/dev/null 2>&1 || true
    $SUDO systemctl restart "$fpm_service" || true
  fi
}

show_summary() {
  log "Resumo"
  echo "Projeto: $PROJECT_DIR"
  echo "SO: $OS_ID/$OS_CODENAME"
  echo "PHP: $($PHP_BIN -v | head -n 1)"
  echo "Composer: $(composer --version | head -n 1)"
  echo "Python: $(python3 --version)"
  echo "Node: $(node -v 2>/dev/null || echo 'não instalado')"
  echo "Nginx: $(nginx -v 2>&1)"
  echo
  echo "Instalação concluída."
  echo "Revise o arquivo .env e configure o Nginx (server_name/SSL) antes de abrir em produção."
}

main() {
  log "Iniciando instalação completa do servidor"
  detect_os
  apt_update_upgrade
  install_base_packages
  configure_php_repository
  install_php_stack
  install_composer
  install_node_if_needed
  prepare_application
  restart_services
  show_summary
}

main "$@"
