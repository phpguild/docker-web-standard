## ----------------------------------------------------------
## ENV
## ----------------------------------------------------------
APP_NAME := myapp

## ----------------------------------------------------------
## Includes
## ----------------------------------------------------------
include ./.make/*

## ----------------------------------------------------------
## Main
## ----------------------------------------------------------
.PHONY: install update restart

install:
	make docker/start
	make proxy/install
	make composer/install
	make filesystem/install
	make listening

update:
	make docker/down
	make git/update
	make docker/start
	make composer/install
	make filesystem/install
	make listening

restart:
	make docker/restart
	make listening

## ----------------------------------------------------------
## Filesystem
## ----------------------------------------------------------
.PHONY: filesystem/install

filesystem/install:
ifeq (prod, $(APP_ENV))
	chown -R 1000:1000 ./var
endif

## ----------------------------------------------------------
## APP
## ----------------------------------------------------------
