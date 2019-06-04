FROM nginx:1.15.8

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# APP environment
ARG APP_ENV

# Nginx Extra
RUN apt-get update \
    && apt-get install -y nginx-extras libnginx-mod-http-headers-more-filter

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Permissions
RUN usermod -u 1000 www-data

# Workdir
WORKDIR /var/www
