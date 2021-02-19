## ----------------------------------------------------------
## Includes
## ----------------------------------------------------------
-include ./.make/bootstrap

###> phpguild/docker-web-standard ###
-include ./vendor/phpguild/docker-web-standard/.make/crontab
-include ./vendor/phpguild/docker-web-standard/.make/git
-include ./vendor/phpguild/docker-web-standard/.make/logrotate
-include ./vendor/phpguild/docker-web-standard/.make/mysql
-include ./vendor/phpguild/docker-web-standard/.make/proxy
-include ./vendor/phpguild/docker-web-standard/.make/setup
-include ./vendor/phpguild/docker-web-standard/.make/ssl
###< phpguild/docker-web-standard ###

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
.PHONY: app/install app/update

app/install:

app/update:
