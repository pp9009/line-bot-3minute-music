FROM php:8.1-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip \
    && docker-php-ext-install bcmath pdo_mysql mbstring sockets

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# changing DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite
