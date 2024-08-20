FROM php:8.2-apache AS base

ARG APCU_VERSION=5.1.22
ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nano \
    vim \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql gd zip opcache
RUN docker-php-ext-configure opcache --enable-opcache
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu

FROM base AS build

WORKDIR /var/www/html

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN chown -R www-data.www-data /var/www

COPY --chown=www-data:www-data config.php /path/to/install/includes/config.php
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data docker/config.lua /home/$user/forgottenserver
COPY --chown=www-data:www-data docker/config.local.php /var/www/html
COPY --chown=www-data:www-data . .
COPY . /var/www/html


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && mkdir -p /var/www/html/install

EXPOSE 80

CMD ["apache2-foreground"]
