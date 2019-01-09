$(shell if [ ! -f ./.env.local ]; then cp ./.env ./.env.local; fi)
include $(realpath .)/.env
include $(realpath .)/.env.local
export

## ----------------------------------------------------------
## Common
## ----------------------------------------------------------
.PHONY: install update restart

install:
	make docker/start
	make install/composer
	make install/proxy

update:
	make docker/down
	make update/git
	make install

restart:
	make docker/down
	make docker/start

## ----------------------------------------------------------
## Docker
## ----------------------------------------------------------
.PHONY: docker/start docker/stop docker/down

docker/start:
	docker-compose up -d --build

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
	docker exec -it myapp_php bash -c "APP_ENV=prod php -d memory_limit=-1 /usr/bin/composer --no-dev --optimize-autoloader -v install"
else
	docker exec -u 1000 -it myapp_php php -d memory_limit=-1 /usr/bin/composer update
endif

## ----------------------------------------------------------
## Proxy
## ----------------------------------------------------------
.PHONY: install/proxy install/proxy/prod install/proxy/preprod install/proxy/dev

install/proxy:
ifeq (prod, $(APP_ENV))
	make install/proxy/prod
	make install/proxy/preprod
	service nginx reload
endif

install/proxy/prod:
ifeq ("$(wildcard /etc/nginx/sites-enabled/myapp.prod.conf)","")
	ln -s "${PWD}/config/nginx/proxies/prod.conf" "/etc/nginx/sites-enabled/myapp.prod.conf"
endif

install/proxy/preprod:
ifeq ("$(wildcard /etc/nginx/sites-enabled/myapp.preprod.conf)","")
	ln -s "${PWD}/config/nginx/proxies/preprod.conf" "/etc/nginx/sites-enabled/myapp.preprod.conf"
endif

install/proxy/dev:
ifeq ("$(wildcard /etc/nginx/sites-enabled/myapp.dev.conf)","")
	ln -s "${PWD}/config/nginx/proxies/dev.conf" "/etc/nginx/sites-enabled/myapp.dev.conf"
endif

## ----------------------------------------------------------
## SSL
## ----------------------------------------------------------
.PHONY: install/ssl/dev

install/ssl/dev:
ifeq ("$(wildcard /etc/nginx/certs/selfsigned.crt)","")
	mkdir -p /etc/nginx/certs
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certs/selfsigned.key -out /etc/nginx/certs/selfsigned.crt
	openssl dhparam -out /etc/nginx/certs/dhparams.pem 2048
	grep -q -F '127.0.0.1 myapp.test' /etc/hosts || echo '127.0.0.1 myapp.test' >> /etc/hosts
endif

## ----------------------------------------------------------
## Dump
## ----------------------------------------------------------
.PHONY: dump/mysql

dump/mysql:
	docker exec myapp_database bash -c '/usr/bin/mysqldump -uroot -p"$(MYSQL_ROOT_PASSWORD)" "$(MYSQL_DATABASE)" > "/tmp/$(MYSQL_DATABASE).sql"'
	docker exec myapp_database cat "/tmp/$(MYSQL_DATABASE).sql" > "./data/$(MYSQL_DATABASE).sql"
