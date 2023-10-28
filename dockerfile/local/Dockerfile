FROM php:8.2-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
COPY ./ /var/www/html

# Install Laravel framework system requirements (https://laravel.com/docs/9.x/deployment#server-requirements)
RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip \
    && docker-php-ext-install pdo_mysql mbstring bcmath sockets \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# changing DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite