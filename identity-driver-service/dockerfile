# Altere de php:8.3-fpm para:
FROM php:8.4-fpm

# O resto do seu Dockerfile permanece exatamente igual:
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

USER www-data
