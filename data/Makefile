## ----------------------------------------------------------
## ENV
## ----------------------------------------------------------
$(shell if [ ! -f .env.local ]; then cp .env .env.local; fi)
include .env
include .env.local
export

DOCKER_PHP := myapp_php_${APP_INSTANCE}
DOCKER_DATABASE := myapp_database_${APP_INSTANCE}

## ----------------------------------------------------------
## Common
## ----------------------------------------------------------
.PHONY: install update restart

install:
	make docker/start
	make install/proxy
	make install/composer
	make install/filesystem
	make listening

update:
	make docker/down
	make update/git
	make docker/start
	make install/composer
	make install/filesystem
	make listening

restart:
	make docker/down
	make docker/start
	make listening

listening:
	@echo "Server listening on http://127.0.0.1:${APP_PORT}"

## ----------------------------------------------------------
## Docker
## ----------------------------------------------------------
.PHONY: docker/start docker/stop docker/down

docker/start:
	docker-compose up -d --build --remove-orphans

docker/stop:
	docker-compose stop

docker/down:
	docker-compose down

## ----------------------------------------------------------
## GIT
## ----------------------------------------------------------
.PHONY: update/git

update/git:
	$(eval BRANCH := $(shell git rev-parse --abbrev-ref HEAD))
	git fetch
	git checkout "$(BRANCH)"
	git reset --hard "origin/$(BRANCH)"

## ----------------------------------------------------------
## Composer
## ----------------------------------------------------------
.PHONY: install/composer

install/composer:
ifeq (prod, $(APP_ENV))
	docker exec ${DOCKER_PHP} bash -c "APP_ENV=prod php -d memory_limit=-1 /usr/bin/composer --no-dev --optimize-autoloader -v install"
else
	docker exec -u 1000 ${DOCKER_PHP} php -d memory_limit=-1 /usr/bin/composer update
endif

## ----------------------------------------------------------
## Proxy
## ----------------------------------------------------------
.PHONY: install/proxy

install/proxy:
ifeq (prod, $(APP_ENV))
ifeq ("$(wildcard /etc/nginx/sites-enabled/myapp.$(APP_INSTANCE).conf)","")
	ln -s "${PWD}/config/nginx/proxies/$(APP_INSTANCE).conf" "/etc/nginx/sites-enabled/myapp.$(APP_INSTANCE).conf"
	service nginx reload
endif
endif

## ----------------------------------------------------------
## SSL
## ----------------------------------------------------------
.PHONY: install/ssl

install/ssl:
ifeq (dev, $(APP_ENV))
ifeq ("$(wildcard /etc/nginx/certs/selfsigned.crt)","")
	mkdir -p /etc/nginx/certs
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certs/selfsigned.key -out /etc/nginx/certs/selfsigned.crt
	openssl dhparam -out /etc/nginx/certs/dhparams.pem 2048
	grep -q -F '127.0.0.1 myapp.test' /etc/hosts || echo '127.0.0.1 myapp.test' >> /etc/hosts
endif
endif

## ----------------------------------------------------------
## Filesystem
## ----------------------------------------------------------
.PHONY: install/filesystem

install/filesystem:
ifeq (prod, $(APP_ENV))
	chown -R 1000:1000 ./var
endif

## ----------------------------------------------------------
## Dump
## ----------------------------------------------------------
.PHONY: dump/mysql restore/mysql

dump/mysql:
	docker exec ${DOCKER_DATABASE} bash -c '/usr/bin/mysqldump -uroot -p"$(MYSQL_ROOT_PASSWORD)" "$(MYSQL_DATABASE)" > "/tmp/$(MYSQL_DATABASE).sql"'
	docker exec ${DOCKER_DATABASE} cat "/tmp/$(MYSQL_DATABASE).sql" > "./data/$(MYSQL_DATABASE).sql"

restore/mysql:
	cat ./dump.sql | docker exec -i ${DOCKER_DATABASE} /usr/bin/mysql -u root -p${MYSQL_ROOT_PASSWORD} ${MYSQL_DATABASE}
