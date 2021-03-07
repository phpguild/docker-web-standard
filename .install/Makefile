-include ./.make/bootstrap

## ----------------------------------------------------------
## Filesystem
## ----------------------------------------------------------
.PHONY: filesystem/install

filesystem/install:
ifeq ($(APP_ENV),prod)
	$(SUDO) chown -R $(APP_UID):$(APP_UID) ./var
endif

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
.PHONY: app/install app/update

app/install:

app/update:
