FROM php:7.2-fpm-stretch

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# Update
RUN apt-get update

# Global
RUN apt-get install -y git

# PHP MbString
RUN docker-php-ext-install mbstring

# PHP MySql
RUN docker-php-ext-install pdo_mysql

# PHP Exif
RUN docker-php-ext-install exif

# PHP ZIP
RUN apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip

# PHP Intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-configure intl

# PHP GD
RUN apt-get install -y libfreetype6-dev libjpeg-dev libpng-dev libgd-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/lib/ --with-jpeg-dir=/usr/lib/ --with-png-dir=/usr/lib/ --with-gd \
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

# Timezone
RUN rm /etc/localtime \
    && ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime \
    && date

# Composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer \
    && chmod +x /usr/bin/composer

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/*

# SSH
RUN echo "StrictHostKeyChecking no" >> /etc/ssh/ssh_config

# Permissions
RUN usermod -u 1000 www-data

WORKDIR /var/www
