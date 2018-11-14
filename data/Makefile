.PHONY: install update composer start stop down

include $(realpath .)/.env
export

BRANCH := $(shell git rev-parse --abbrev-ref HEAD)

start:
	docker-compose up -d --build

stop:
	docker-compose stop

down:
	docker-compose down

install:
	make start
	make composer

update:
	make down
	git fetch
	git checkout "$(BRANCH)"
	git reset --hard "origin/$(BRANCH)"
	make install

composer:
ifeq (prod, $(APP_ENV))
		docker exec -it myapp_php bash -c "APP_ENV=prod php -d memory_limit=-1 /usr/bin/composer --no-dev --optimize-autoloader -v install"
else
		docker exec -u 1000 -it myapp_php php -d memory_limit=-1 /usr/bin/composer update
endif
