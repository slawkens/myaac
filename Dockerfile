FROM docker.io/node:22-bookworm-slim AS npm

COPY . /myaac
WORKDIR /myaac
RUN npm install

FROM docker.io/php:8.4-apache-bookworm

ENV MYAAC_DOCKER=1

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update -yq && apt-get install libpng-dev libzip-dev -yq && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install gd pdo_mysql zip

WORKDIR /var/www/html

COPY --from=npm --chown=www-data:www-data /myaac .
COPY --from=docker.io/composer /usr/bin/composer /usr/bin/composer
RUN composer install
