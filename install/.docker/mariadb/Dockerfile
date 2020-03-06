FROM mariadb:10.3.21

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

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Permissions
RUN usermod -u ${APP_UID} www-data

# Workdir
WORKDIR /var/www
