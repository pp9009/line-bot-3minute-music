FROM php:8.1-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY .env.example .env
COPY ./php.ini /usr/local/etc/php/php.ini

RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip \
    && docker-php-ext-install pdo_mysql mbstring sockets

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer install
# RUN chown -Rf www-data:www-data ./
