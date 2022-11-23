FROM php:8.1-fpm

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

COPY . .

RUN apt-get update \
    && apt-get install -y libonig-dev git zip unzip \
    && docker-php-ext-install pdo_mysql mbstring sockets

CMD ["/start.sh"]