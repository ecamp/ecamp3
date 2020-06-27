.PHONY: setup install run print

setup:
	# install frontend dependencies with npm
	./frontend/setup.sh
	docker-compose run --rm frontend npm ci

	# install print dependencies with npm
	docker-compose run --rm print npm ci

	# install backend dependencies with composer
	docker-compose run --rm composer

	# setup database & load PROD + DEV fixtures
	docker-compose up -d db
	docker-compose run --rm --entrypoint ./setup.sh backend 
	docker-compose stop

install:
	docker-compose run --rm frontend npm i
	docker-compose run --rm print npm i
	docker-compose run --rm composer

docker-build:
	docker-compose build

run:
	docker-compose up -d frontend backend print db phpmyadmin
	docker-compose logs -f frontend

print:
	docker-compose run --rm worker-print-puppeteer