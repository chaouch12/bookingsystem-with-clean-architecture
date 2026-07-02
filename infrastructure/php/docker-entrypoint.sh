#!/bin/sh
set -eu

cd /var/www/backend

if [ -f composer.json ] && [ "${SKIP_COMPOSER_INSTALL:-0}" != "1" ]; then
    composer install --no-interaction --prefer-dist --no-scripts
fi

exec docker-php-entrypoint "$@"
