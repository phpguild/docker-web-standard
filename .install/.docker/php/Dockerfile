FROM php:8.0.11-fpm-buster

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# APP environment
ARG APP_ENV='prod'
ARG APP_UID=1000
ARG APP_GID=33
ARG APP_USER='www-data'
ARG TZ='Europe/Paris'
ARG BLACKFIRE_ENABLE=0

# Timezone
RUN rm /etc/localtime \
    && ln -s /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

# Update
RUN rm -f /etc/apt/sources.list.d/*
RUN apt-get update

# Global
RUN apt-get install -y git

# PHP MbString
RUN apt-get install -y libonig-dev \
    && docker-php-ext-install mbstring

# PHP Exif
RUN docker-php-ext-install exif

# PHP ZIP
RUN apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install zip

# PHP Intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-install intl

# PHP GD
RUN apt-get install -y libfreetype6-dev libjpeg-dev libpng-dev libgd-dev libheif-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

# PHP Imagick
RUN apt-get install -y libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# PHP Gmagick
#RUN apt-get install -y libgraphicsmagick1-dev \
#    && pecl install gmagick-2.0.6RC1 \
#    && docker-php-ext-enable gmagick

# PHP sysvsem
RUN docker-php-ext-install sysvsem

# PHP MySql
RUN docker-php-ext-install pdo_mysql mysqli

# PHP PostgreSQL
#RUN apt-get install -y libpq-dev \
#    && docker-php-ext-install pdo_pgsql pgsql

# PHP Apcu
RUN if [ "prod" = "${APP_ENV}" ] ; then \
    mkdir -p /usr/src/php/ext/apcu \
        && curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 \
        && docker-php-ext-install apcu \
        && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini \
; fi

# PHP OpCache
RUN if [ "prod" = "${APP_ENV}" ] ; then \
    docker-php-ext-configure opcache --enable-opcache \
        && docker-php-ext-install opcache \
; fi

# PHP blackfire
RUN if [ 1 = "${BLACKFIRE_ENABLE}" ] ; then \
    version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
        && architecture=$(case $(uname -m) in i386 | i686 | x86) echo "i386" ;; x86_64 | amd64) echo "amd64" ;; aarch64 | arm64 | armv8) echo "arm64" ;; *) echo "amd64" ;; esac) \
        && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/$architecture/$version \
        && mkdir -p /tmp/blackfire \
        && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
        && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
        && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
        && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz \
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
RUN sed -i "s/${APP_USER}:x:1000:33/${APP_USER}:x:${APP_UID}:${APP_GID}/g" /etc/passwd \
    && mkdir -p "/home/${APP_USER}" \
    && chown -R ${APP_UID}:${APP_GID} "/home/${APP_USER}" \
    && usermod -d "/home/${APP_USER}" ${APP_USER} \
    && usermod -u ${APP_UID} ${APP_USER}

# PHP sessions
RUN mkdir -p /var/lib/php/sessions \
    && chown -R ${APP_UID}:${APP_GID} /var/lib/php/sessions

# Workdir
WORKDIR /var/www
