# Docker Web Standard

Docker installer for web app, include :

- PHP 8.0.11
- MariaDB 10.6.4 or PostgreSQL 9.6.23
- Nginx 1.21.3
- Blackfire (for dev)

## Require from composer

    composer req phpguild/docker-web-standard

## Installation

    make -e APP_NAME=__dwsmyapp__ install

## Usage

### Restart

    make restart

### Down

    make down
