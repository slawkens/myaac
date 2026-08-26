FROM docker.io/node:24-trixie-slim AS npm

WORKDIR /myaac

COPY . .
RUN mv .htaccess.dist .htaccess && npm install

FROM docker.io/php:8.4-apache-trixie

ARG APCU_VERSION=5.1.28

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
	libpng-dev \
	libzip-dev \
	rsync \
	zip \
	unzip \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu

RUN a2enmod rewrite && docker-php-ext-install pdo_mysql gd zip

# Create system user to run Composer Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
	mkdir -p /home/$user/server && \
	chown -R $user:$user /home/$user

RUN chown -R www-data:www-data /var/www

RUN mkdir -p /config && chown -R www-data:www-data /config

USER $user

COPY --chown=www-data:www-data docker/config.lua /home/$user/server

WORKDIR /var/www/html

COPY --from=npm --chown=www-data:www-data /myaac .
COPY --from=docker.io/composer /usr/bin/composer /usr/bin/composer

RUN rm -rf /var/www/html/node_modules \
	&& chown -R www-data:www-data /var/www/html

RUN echo '*' > install/ip.txt && composer install

USER root

COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/myaac-entrypoint

ENTRYPOINT ["myaac-entrypoint"]
CMD ["bash", "-c", "sh /usr/local/bin/sync-windows-files.sh & apache2-foreground"]
