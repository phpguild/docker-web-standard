# Docker Web Standard

Docker installer for web app, include :

- PHP 7.4.15
- MariaDB 10.5.8
- Nginx 1.19.6

## Require from composer

    composer req phpguild/docker-web-standard

## Installation

    make -e APP_NAME=__dwsmyapp__ install

## Usage

### Restart

    make restart

### Down

    make down
