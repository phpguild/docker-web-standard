FROM nginx:1.15

MAINTAINER Jonathan SAHM <contact@johnstyle.fr>

# Update
RUN apt-get update

# Global
RUN apt-get install -y git zip ssh

# Clean
RUN apt-get autoremove --purge -y \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/*
