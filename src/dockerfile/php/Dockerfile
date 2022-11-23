FROM php:8.1.5-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/html

COPY . ./

RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip \
    && docker-php-ext-install pdo_mysql mbstring sockets

RUN composer install
RUN chown -Rf www-data:www-data ./
