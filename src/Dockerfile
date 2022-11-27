FROM composer AS composer

# copying the source directory and install the dependencies with composer
COPY ./ /app
COPY .env.example /app/.env

RUN docker-php-ext-install sockets

# run composer install to install the dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# continue stage build with the desired image and copy the source including the
# dependencies downloaded by composer
FROM trafex/php-nginx:latest

USER root

# Install Laravel framework system requirements (https://laravel.com/docs/9.x/deployment#server-requirements)
RUN apk add \
        php81-bcmath \
        php81-fileinfo \
        php81-tokenizer \
        php81-mbstring \
        php81-pdo_mysql

COPY ./conf/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./php.ini /usr/local/etc/php/
COPY --chown=nginx --from=composer /app /var/www/html
RUN chown -R nobody:nobody /var/www/html/storage
USER nobody
