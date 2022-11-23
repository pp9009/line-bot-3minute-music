FROM php:8.1-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip\
    && docker-php-ext-install pdo_mysql mbstring sockets

WORKDIR /var/www/html
COPY . ./
COPY ./php.ini /usr/local/etc/php/
COPY ./conf/apache/*.conf /etc/apache2/sites-enabled/

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer install
# RUN chown -Rf www-data:www-data ./
RUN chown www-data storage/ -R
