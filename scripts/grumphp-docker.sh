#!/bin/sh

set -eu

REPO_ROOT="$(git rev-parse --show-toplevel)"
COMPOSE_FILE="$REPO_ROOT/infrastructure/docker-compose.yml"
ENV_FILE="$REPO_ROOT/.env"
COMPOSE_PROJECT="${COMPOSE_PROJECT:-booking-system}"
CONTAINER_GIT_DIR="/var/www/project"
CONTAINER_BACKEND_DIR="/var/www/project/backend"

COMPOSE_ARGS=(-f "$COMPOSE_FILE" -p "$COMPOSE_PROJECT")
if [ -f "$ENV_FILE" ]; then
    COMPOSE_ARGS+=(--env-file "$ENV_FILE")
fi

exec docker compose "${COMPOSE_ARGS[@]}" exec -T \
    -e XDEBUG_MODE=off \
    -e GRUMPHP_GIT_WORKING_DIR="$CONTAINER_GIT_DIR" \
    -e GRUMPHP_PROJECT_DIR="$CONTAINER_BACKEND_DIR" \
    -w "$CONTAINER_BACKEND_DIR" \
    php php "$@" < /dev/null
