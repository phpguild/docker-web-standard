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
    docker-compose exec -it myapp_php composer install
