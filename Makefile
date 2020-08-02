.PHONY: setup install run print

setup:
	docker-compose up

install:
	docker-compose run --rm frontend npm i
	docker-compose run --rm print npm i
	docker-compose run --rm composer

docker-build:
	docker-compose build

run:
	docker-compose up -d db phpmyadmin
	docker-compose run -d --name ecamp3-backend-lean  --service-ports --entrypoint "./docker-run.sh" backend 
	docker-compose run -d --name ecamp3-print-lean    --service-ports print npm run dev
	docker-compose run    --name ecamp3-frontend-lean --service-ports frontend npm run serve


print:
	docker-compose run --rm worker-print-puppeteer
	docker-compose run --rm worker-print-weasy