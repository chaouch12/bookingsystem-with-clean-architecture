#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
INFRA_DIR="${ROOT_DIR}/infrastructure"
BACKEND_DIR="${ROOT_DIR}/backend"

if [[ -f "${ROOT_DIR}/.env" ]]; then
  set -a
  # shellcheck disable=SC1091
  source "${ROOT_DIR}/.env"
  set +a
fi

COMPOSE_FILE="${INFRA_DIR}/docker-compose.yml"
ENV_FILE="${ROOT_DIR}/.env"
DC=(docker compose --env-file "${ENV_FILE}" -f "${COMPOSE_FILE}")

log() {
  printf '\n==> %s\n' "$*"
}

ensure_env() {
  if [[ ! -f "${ROOT_DIR}/.env" ]]; then
    log "Creating .env from .env.example"
    cp "${ROOT_DIR}/.env.example" "${ROOT_DIR}/.env"
  fi

  if [[ ! -f "${BACKEND_DIR}/.env.local" ]]; then
    log "Creating backend/.env.local"
    cat > "${BACKEND_DIR}/.env.local" <<EOF
APP_ENV=dev
APP_SECRET=change-me-in-production-$(openssl rand -hex 16)
DATABASE_URL="mysql://${MYSQL_USER:-booking}:${MYSQL_PASSWORD:-booking}@mysql:3306/${MYSQL_DATABASE:-booking_system}?serverVersion=8.4&charset=utf8mb4"
EOF
  fi
}

cmd_install() {
  log "Installing infrastructure and backend dependencies"

  ensure_env

  if [[ ! -f "${BACKEND_DIR}/composer.json" ]]; then
    log "Symfony project not found. Run: composer create-project symfony/skeleton backend"
    exit 1
  fi

  log "Building Docker images"
  "${DC[@]}" build

  log "Starting containers (detached)"
  "${DC[@]}" up -d

  log "Waiting for MySQL to be healthy"
  for _ in $(seq 1 30); do
    if "${DC[@]}" exec -T mysql mysqladmin ping -h localhost -u root -p"${MYSQL_ROOT_PASSWORD:-root}" --silent 2>/dev/null; then
      break
    fi
    sleep 2
  done

  log "Running composer install in PHP container"
  "${DC[@]}" exec -T php composer install --no-interaction --prefer-dist

  if "${DC[@]}" exec -T php php bin/console list doctrine:migrations:migrate >/dev/null 2>&1; then
    log "Running database migrations"
    "${DC[@]}" exec -T php php bin/console doctrine:migrations:migrate --no-interaction || true
  fi

  log "Install complete"
  cmd_status
}

cmd_up() {
  ensure_env
  log "Starting containers"
  "${DC[@]}" up -d
  cmd_status
}

cmd_down() {
  log "Stopping containers"
  "${DC[@]}" down
}

cmd_reset() {
  log "Stopping containers and removing volumes"
  "${DC[@]}" down -v
}

cmd_logs() {
  "${DC[@]}" logs -f "${@:-}"
}

cmd_shell() {
  "${DC[@]}" exec php bash
}

cmd_composer() {
  "${DC[@]}" exec php composer "$@"
}

cmd_console() {
  "${DC[@]}" exec php php bin/console "$@"
}

cmd_test() {
  "${DC[@]}" exec php php bin/phpunit "$@"
}

cmd_status() {
  log "Service URLs"
  echo "  API (gateway): http://${APP_DOMAIN:-localhost}"
  echo "  API (direct):  http://${APP_DOMAIN:-localhost}:${APP_PORT:-8080}"
  echo "  Mailpit UI:    http://localhost:${MAILPIT_UI_PORT:-8025}"
  echo "  MySQL:         localhost:${MYSQL_PORT:-3306}"
  echo "  Redis:         localhost:${REDIS_PORT:-6379}"
  echo ""
  "${DC[@]}" ps
}

usage() {
  cat <<EOF
Usage: ./infrastructure/dev.sh <command>

Commands:
  install     Build images, start containers, composer install, migrate
  up          Start containers
  down        Stop containers
  reset       Stop containers and remove volumes
  logs [svc]  Follow logs (optional service name)
  shell       Open bash shell in PHP container
  composer    Run composer inside PHP container
  console     Run Symfony console inside PHP container
  test        Run PHPUnit inside PHP container
  status      Show URLs and container status
EOF
}

main() {
  local cmd="${1:-}"
  shift || true

  case "${cmd}" in
    install) cmd_install "$@" ;;
    up) cmd_up "$@" ;;
    down) cmd_down "$@" ;;
    reset) cmd_reset "$@" ;;
    logs) cmd_logs "$@" ;;
    shell) cmd_shell "$@" ;;
    composer) cmd_composer "$@" ;;
    console) cmd_console "$@" ;;
    test) cmd_test "$@" ;;
    status) cmd_status "$@" ;;
    *) usage; exit 1 ;;
  esac
}

main "$@"
