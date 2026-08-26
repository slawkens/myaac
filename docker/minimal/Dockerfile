FROM docker.io/node:24-trixie-slim AS npm

WORKDIR /myaac

COPY . .
RUN mv .htaccess.dist .htaccess && npm install

FROM docker.io/php:8.4-apache-trixie

ENV MYAAC_CONFIG_DIR=/config

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update -yq && apt-get install libpng-dev libzip-dev -yq && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite && docker-php-ext-install gd pdo_mysql zip

WORKDIR /var/www/html

COPY --from=npm --chown=www-data:www-data /myaac .
COPY --from=docker.io/composer /usr/bin/composer /usr/bin/composer
RUN echo '*' > install/ip.txt && composer install

VOLUME /config /var/www/html/system/cache
