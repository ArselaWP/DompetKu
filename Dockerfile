
# PHP base image
FROM php:8.3-cli AS base

# Install kebutuhan PHP dan database
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


# =========================
# TEST STAGE
# =========================
FROM base AS test

COPY . .

RUN composer install --no-interaction --prefer-dist --no-progress

RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan test


# =========================
# APPLICATION STAGE
# =========================
FROM base AS app

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
