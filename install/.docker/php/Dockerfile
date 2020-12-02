FROM php:7.4.13-fpm-buster

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# APP environment
ARG APP_ENV='prod'
ARG APP_UID=1000
ARG APP_TZ='Europe/Paris'

# Timezone
RUN rm /etc/localtime \
    && ln -s /usr/share/zoneinfo/${APP_TZ} /etc/localtime \
    && echo ${APP_TZ} > /etc/timezone

# Update
RUN rm -f /etc/apt/sources.list.d/*
RUN apt-get update

# Global
RUN apt-get install -y git

# PHP MbString
RUN apt-get install -y libonig-dev \
    && docker-php-ext-install mbstring

# PHP MySql
RUN docker-php-ext-install pdo_mysql

# PHP Exif
RUN docker-php-ext-install exif

# PHP ZIP
RUN apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install zip

# PHP Intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-install intl

# PHP GD
RUN apt-get install -y libfreetype6-dev libjpeg-dev libpng-dev libgd-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# PHP Imagick
RUN apt-get install -y libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# PHP Apcu
RUN if [ "${APP_ENV}" = "prod" ] ; then \
    pecl install apcu apcu_bc \
        && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini \
        && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini \
; fi

# PHP OpCache
RUN if [ "${APP_ENV}" = "prod" ] ; then \
    docker-php-ext-configure opcache --enable-opcache \
        && docker-php-ext-install opcache \
; fi

# Composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/var/www/data/composer
ENV COMPOSER_CACHE_DIR=/var/www/data/composer/cache
RUN curl -L "https://getcomposer.org/composer-stable.phar" -o /usr/bin/composer \
    && chmod +x /usr/bin/composer \
    && /usr/bin/composer self-update

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# SSH
RUN echo "StrictHostKeyChecking no\nUserKnownHostsFile=/dev/null" >> /etc/ssh/ssh_config

# Permissions
RUN sed -i "s/www-data:x:1000:33/www-data:x:${APP_UID}:33/g" /etc/passwd \
    && mkdir /home/www-data \
    && chown -R www-data:www-data /home/www-data \
    && usermod -d /home/www-data www-data \
    && usermod -u ${APP_UID} www-data

# Workdir
WORKDIR /var/www
