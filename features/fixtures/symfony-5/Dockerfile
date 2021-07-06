ARG PHP_VERSION
FROM php:$PHP_VERSION

WORKDIR /app

ARG BUGSNAG_API_KEY
ARG BUGSNAG_ENDPOINT

ENV BUGSNAG_API_KEY=$BUGSNAG_API_KEY
ENV BUGSNAG_ENDPOINT=$BUGSNAG_ENDPOINT

RUN apt-get update && \
  apt-get install -y --no-install-recommends \
  git \
  zip \
  unzip

COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
