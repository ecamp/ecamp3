setup:
	./frontend/setup.sh
	docker-compose run --rm frontend npm ci

install:
	docker-compose run --rm frontend npm i

dev:
	docker-compose up