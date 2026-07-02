#!/bin/sh

set -eu

REPO_ROOT="$(git rev-parse --show-toplevel)"
COMPOSE_FILE="$REPO_ROOT/infrastructure/docker-compose.yml"
COMPOSE_PROJECT="${COMPOSE_PROJECT:-booking-system}"
CONTAINER_GIT_DIR="/var/www/project"
CONTAINER_BACKEND_DIR="/var/www/project/backend"

exec docker compose -f "$COMPOSE_FILE" -p "$COMPOSE_PROJECT" exec -T \
    -e GRUMPHP_GIT_WORKING_DIR="$CONTAINER_GIT_DIR" \
    -w "$CONTAINER_GIT_DIR" \
    php php "$@"
