# ==========================================
# STAGE 1: Builder (Hanya untuk Composer)
# ==========================================
FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --ignore-platform-reqs \
    --no-scripts \
    --optimize-autoloader

# ==========================================
# STAGE 2: Production (Image Final)
# ==========================================
FROM dunglas/frankenphp:1-php8.5-alpine

# Install PHP Extensions & Supervisor
RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    pgsql \
    pdo_pgsql \
    imagick \
    redis \
    zip \
    opcache && \
    apk add --no-cache supervisor

# Copy custom php.ini
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/99-overrides.ini

WORKDIR /app

# Copy source code & vendor
COPY . .
COPY --from=vendor /app/vendor /app/vendor

# Optimasi Laravel & Permissions
RUN rm -f bootstrap/cache/*.php && \
    php artisan package:discover --ansi && \
    chown -R www-data:www-data storage bootstrap/cache

# Konfigurasi Supervisor
# Pastikan Anda membuat file ini di folder .docker/supervisor/supervisord.conf
COPY .docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script Entrypoint
# Pastikan Anda membuat file ini di folder .docker/entrypoint.sh
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Environment variables
ENV AUTOCONF_SERVER=frankenphp \
    LARAVEL_OCTANE=1 \
    SUPERVISOR_PHP_USER=www-data

# Expose ports
EXPOSE 3300 443 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]