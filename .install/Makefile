-include ./.make/bootstrap

## ----------------------------------------------------------
## Filesystem
## ----------------------------------------------------------
.PHONY: filesystem/install

filesystem/install:
	$(SUDO) chmod 777 ./config/crontab/*
	$(SUDO) chown -R $(APP_UID):$(APP_UID) ./var

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
.PHONY: app/install app/update

app/install:

app/update:
