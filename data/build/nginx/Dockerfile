FROM nginx:1.15

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# Update
RUN apt-get update

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/*
