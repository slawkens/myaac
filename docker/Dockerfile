FROM php:8.2-apache

ARG APCU_VERSION=5.1.22

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip opcache

RUN docker-php-ext-configure opcache --enable-opcache

#INSTALL APCU
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu
RUN echo "extension=apcu.so" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable=1" >> /usr/local/etc/php/php.ini
#APCU

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Create system user to run Composer Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN chown -R www-data.www-data /var/www

USER $user

WORKDIR /home/$user
RUN git clone https://github.com/otland/forgottenserver.git

COPY docker/config.lua /home/$user/forgottenserver

#WORKDIR /home/$user/forgottenserver

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
RUN composer install
