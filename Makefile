.PHONY: setup install run print

setup:
	docker-compose up

install:
	docker-compose run --rm frontend npm i
	docker-compose run --rm print npm i
	docker-compose run --rm composer
	docker-compose run --rm worker-print-puppeteer npm i

docker-build:
	docker-compose build

run-backend:
	docker-compose up -d db phpmyadmin
	docker-compose run -d --name backend           --service-ports --entrypoint "./docker-setup.sh" backend 

run-frontend-vuecli:
	docker-compose run    --name frontend          --service-ports frontend npm run serve

run-frontend-vite:
	docker-compose run    --name frontend          --service-ports frontend npm run dev

run-printer:
	docker-compose up -d rabbitmq print-file-server
	docker-compose run -d --name print --service-ports print npm run dev
	docker-compose up -d worker-print-puppeteer
	docker-compose up -d worker-print-weasy

test:
	docker exec -it backend composer test
	docker exec -it forntend npm run test:unit

lint:
	docker exec -it backend ./vendor/bin/php-cs-fixer fix