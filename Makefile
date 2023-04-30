run: install
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app php artisan migrate
	docker-compose ps -a

build: install
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app php artisan optimize
	docker-compose exec app php artisan migrate
	docker-compose ps -a

install: down
	ls .data || mkdir .data
	docker-compose up -d --build
	docker-compose exec app composer install

uninstall: down
	cd ../ && rm -rf fluxo-financeiro-api

status:
	docker-compose ps -a

down:
	docker-compose down
	docker-compose ps -a
