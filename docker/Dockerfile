FROM php:8.2-fpm

# 基本パッケージ
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

# Xdebugインストール
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
