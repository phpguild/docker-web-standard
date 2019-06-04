## ----------------------------------------------------------
## ENV
## ----------------------------------------------------------
APP_NAME := myapp

## ----------------------------------------------------------
## Includes
## ----------------------------------------------------------
-include ./.make/bootstrap
-include ./vendor/phpguild/docker-web-standard/.make/git
-include ./vendor/phpguild/docker-web-standard/.make/mysql
-include ./vendor/phpguild/docker-web-standard/.make/proxy
-include ./vendor/phpguild/docker-web-standard/.make/ssl

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
.PHONY: app/install app/update

app/install:
	echo "install"

app/update:
	echo "update"
