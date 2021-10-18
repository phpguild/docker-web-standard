FROM nginx:1.21.3

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# APP environment
ARG APP_ENV='prod'
ARG APP_UID=1000
ARG APP_GID=33
ARG APP_USER='www-data'
ARG TZ='Europe/Paris'

# Timezone
RUN rm /etc/localtime \
    && ln -s /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

# Update
RUN rm -f /etc/apt/sources.list.d/*
RUN apt-get update

# Nginx Extra
RUN apt-get install -y nginx-extras libnginx-mod-http-headers-more-filter

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Permissions
RUN usermod -u ${APP_UID} ${APP_USER}

# Workdir
COPY ./ /var/www
