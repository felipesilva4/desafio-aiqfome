FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

RUN pecl install pcov && docker-php-ext-enable pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
