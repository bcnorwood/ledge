FROM php:fpm-alpine

COPY ./app ${APP_ROOT}

 RUN docker-php-ext-install pdo_mysql && \
    curl https://getcomposer.org/installer | php && \
    ./composer.phar install
