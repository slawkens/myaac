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
    unzip \
    nano \
    vim

RUN apt-get install -y nodejs npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip opcache
RUN docker-php-ext-configure opcache --enable-opcache
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN chown -R www-data.www-data /var/www

USER $user

WORKDIR /home/$user
RUN git clone https://github.com/otland/forgottenserver.git

COPY --chown=www-data:www-data docker/config.lua /home/$user/forgottenserver
COPY --chown=www-data:www-data docker/config.local.php /var/www/html

#WORKDIR /home/$user/forgottenserver

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
RUN composer install
RUN npm install
