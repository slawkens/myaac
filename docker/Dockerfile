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
	git \
	libpng-dev \
	libzip-dev \
	rsync \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu

RUN a2enmod rewrite && docker-php-ext-install pdo_mysql gd zip

# Create system user to run Composer Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

USER $user

WORKDIR /home/$user
#RUN mkdir -p forgottenserver
RUN git clone https://github.com/otland/forgottenserver.git
#ADD https://raw.githubusercontent.com/otland/forgottenserver/refs/heads/master/schema.sql /docker-entrypoint-initdb.d/tfs_schema.sql

COPY --chown=www-data:www-data docker/config.lua /home/$user/forgottenserver

WORKDIR /var/www/html

COPY --from=npm --chown=www-data:www-data /myaac .
COPY --from=docker.io/composer /usr/bin/composer /usr/bin/composer

RUN rm -rf /var/www/html/node_modules \
	&& chown -R www-data:www-data /var/www/html

USER root
RUN mkdir -p /myaac-main-folder
RUN echo "#!/bin/bash" > sync-windows-files.sh
RUN echo 'while true; do' >> sync-windows-files.sh
RUN echo '  rsync -au --exclude-from="/myaac-main-folder/.dockerignore" "/myaac-main-folder/" "/var/www/html"' >> sync-windows-files.sh
RUN echo '  sleep 2;' >> sync-windows-files.sh
RUN echo 'done' >> sync-windows-files.sh

USER $user

RUN echo '*' > install/ip.txt && composer install

USER root

CMD ["bash", "-c", "sh sync-windows-files.sh & apache2-foreground"]
