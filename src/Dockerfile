FROM php:8.1-apache

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Install Laravel framework system requirements (https://laravel.com/docs/9.x/deployment#server-requirements)
RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip\
    && docker-php-ext-install pdo_mysql mbstring bcmath sockets

WORKDIR /var/www/html
COPY ./ ./
COPY .env.example .env
COPY ./php.ini /usr/local/etc/php/

# run composer install to install the dependencies
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --no-interaction --optimize-autoloader --no-dev

# changing DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

RUN chown -R www-data /var/www/html/storage