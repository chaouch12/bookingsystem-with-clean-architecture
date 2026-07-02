#!/usr/bin/env bash

function setupXdebugINI() {
  local port="${1:-}"

  if [[ -z "${port}" ]]; then
    echo "setupXdebugINI: missing port argument" >&2
    return 1
  fi

  if [[ ! -f /etc/php/xdebug.ini || ! -f /etc/php/xdebug-cli.ini ]]; then
    echo "setupXdebugINI: missing xdebug templates in /etc/php/" >&2
    return 1
  fi

  if [[ -d "/etc/php/${PHP_VERSION}/fpm/conf.d" ]]; then
    # Debian/php-devbox layout (docker-localdev compatible)
    local xdebug_fpm_ini="/etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini"
    local xdebug_cli_ini="/etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini"

    rm -f "${xdebug_fpm_ini}" "${xdebug_cli_ini}"

    cp /etc/php/xdebug.ini "${xdebug_fpm_ini}"
    cp /etc/php/xdebug-cli.ini "${xdebug_cli_ini}"

    sed -i "s/__PORT-PLACEHOLDER__/${port}/g" "${xdebug_fpm_ini}" "${xdebug_cli_ini}"
  else
    # Official php:*-fpm image: xdebug.mode must live in conf.d (not php-fpm pool).
    # CLI uses XDEBUG_MODE=off in entrypoint/scripts when needed.
    local xdebug_ini="/usr/local/etc/php/conf.d/20-xdebug.ini"

    rm -f "${xdebug_ini}" \
      /usr/local/etc/php/conf.d/99-xdebug.ini \
      /usr/local/etc/php/conf.d/99-xdebug-cli.ini \
      /usr/local/etc/php-fpm.d/zz-xdebug.conf

    cp /etc/php/xdebug.ini "${xdebug_ini}"
    sed -i "s/__PORT-PLACEHOLDER__/${port}/g" "${xdebug_ini}"
  fi
}
