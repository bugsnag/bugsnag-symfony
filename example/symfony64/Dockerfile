FROM php:8.3-fpm

WORKDIR /app

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY composer.json /app/composer.json
COPY bin /app/bin

RUN composer install

COPY . /app/

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
