FROM php:8.4-fpm-bookworm

COPY php/docker-entrypoint.sh /docker-entrypoint.sh
COPY php/php.ini /usr/local/etc/php/conf.d/99-booking-system.ini

RUN chmod +x /docker-entrypoint.sh \
  && apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    $PHPIZE_DEPS \
  && docker-php-ext-install -j"$(nproc)" \
    intl \
    opcache \
    pdo_mysql \
    zip \
  && pecl install redis xdebug \
  && docker-php-ext-enable redis \
  && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $PHPIZE_DEPS \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  && usermod -u 1000 www-data \
  && groupmod -g 1000 www-data

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["php-fpm"]
