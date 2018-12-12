$(shell if [ ! -f ./.env.local ]; then cp ./.env ./.env.local; fi)
include $(realpath .)/.env
include $(realpath .)/.env.local
export

## Install
.PHONY: start restart stop down install update

start:
	docker-compose up -d --build

restart:
	make down
	make start

stop:
	docker-compose stop

down:
	docker-compose down

install:
	make start
	make composer
ifeq (prod, $(APP_ENV))
	service nginx reload
endif

update:
	$(eval BRANCH := $(shell git rev-parse --abbrev-ref HEAD))
	make down
	git fetch
	git checkout "$(BRANCH)"
	git reset --hard "origin/$(BRANCH)"
	make install

## Composer
.PHONY: composer

composer:
ifeq (prod, $(APP_ENV))
		docker exec -it myapp_php bash -c "APP_ENV=prod php -d memory_limit=-1 /usr/bin/composer --no-dev --optimize-autoloader -v install"
else
		docker exec -u 1000 -it myapp_php php -d memory_limit=-1 /usr/bin/composer update
endif

## Dump
.PHONY: dump/mysql

dump/mysql:
	docker exec myapp_database bash -c '/usr/bin/mysqldump -uroot -p"$(MYSQL_ROOT_PASSWORD)" "$(MYSQL_DATABASE)" > "/tmp/$(MYSQL_DATABASE).sql"'
	docker exec myapp_database cat "/tmp/$(MYSQL_DATABASE).sql" > "./data/$(MYSQL_DATABASE).sql"

## Proxy
.PHONY: install/proxy

install/proxy:
	ln -s "${PWD}/config/nginx/proxies/$(APP_ENV).conf" "/etc/nginx/sites-enabled/myapp.conf"

## SSL
.PHONY: install/ssl

install/ssl:
ifeq ("$(wildcard /etc/nginx/certs/selfsigned.crt)","")
	mkdir -p /etc/nginx/certs
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certs/selfsigned.key -out /etc/nginx/certs/selfsigned.crt
	openssl dhparam -out /etc/nginx/certs/dhparams.pem 2048
	grep -q -F '127.0.0.1 dev.local' /etc/hosts || echo '127.0.0.1 dev.local' >> /etc/hosts
endif
