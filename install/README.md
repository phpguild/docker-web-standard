# Project

## Install server

1. If you want install a local server (with reverse proxy), run `sudo make setup/server`

## Install project stack

1. Edit application name `APP_NAME := myapp` into `Makefile`
2. Run `make install`

## Restart project stack

1. Run `make restart`

## Down docker containers

1. Run `make docker/down`
