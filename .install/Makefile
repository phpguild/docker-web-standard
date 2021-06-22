-include ./.make/bootstrap
-include ./vendor/phpguild/docker-web-standard/.make/mysql
#-include ./vendor/phpguild/docker-web-standard/.make/postgres

## ----------------------------------------------------------
## Main
## ----------------------------------------------------------
_preinstall:
	$(SED) "s/__dwsmyapp__/$(APP_NAME)/g" docker-compose*.yml config/nginx/proxies/*.conf .env* README.md

_postinstall:

_preupdate:

_postupdate:

## ----------------------------------------------------------
## Filesystem
## ----------------------------------------------------------
.PHONY: filesystem/install

filesystem/install:
	$(SUDO) chown -fR $(APP_UID):$(APP_UID) ./var || exit 0

## ----------------------------------------------------------
## Database
## ----------------------------------------------------------
.PHONY: database/dump database/restore

database/dump:
	make mysql/dump

database/restore:
	make mysql/restore

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
.PHONY: app/install app/update

app/install:

app/update:
