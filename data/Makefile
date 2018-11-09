.PHONY: run stop start down composer

run:
	docker-compose up -d --build
stop:
	docker-compose stop
start:
	docker-compose up -d
down:
	docker-compose down
composer:
	docker exec -it myapp_php composer --no-dev --optimize-autoloader --no-plugins -v install
