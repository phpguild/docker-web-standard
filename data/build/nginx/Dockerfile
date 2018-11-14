FROM nginx:1.15

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# APP environment
ARG APP_ENV

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/*

# Permissions
RUN usermod -u 1000 www-data

# Workdir
WORKDIR /var/www
