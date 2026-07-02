#!/usr/bin/env bash

. /setup_xdebug.sh

if [[ -n "${XDEBUG_PORT:-}" ]]; then
  mkdir -p /tmp/xdebug
  touch /tmp/xdebug.log
  chmod 777 /tmp/xdebug /tmp/xdebug.log
  setupXdebugINI "${XDEBUG_PORT}"
fi

cd /var/www/backend

if [[ -f composer.json && "${SKIP_COMPOSER_INSTALL:-0}" != "1" ]]; then
  # Avoid Xdebug blocking container boot while Composer runs many PHP processes.
  XDEBUG_MODE=off composer install --no-interaction --prefer-dist --no-scripts
fi

exec docker-php-entrypoint "$@"
