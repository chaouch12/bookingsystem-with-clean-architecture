FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY php.ini /usr/local/etc/php/conf.d/99-booking-system.ini
COPY docker-entrypoint.sh /usr/local/bin/booking-entrypoint.sh
RUN chmod +x /usr/local/bin/booking-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/booking-entrypoint.sh"]
CMD ["php-fpm"]
