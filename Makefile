setup:
	docker-compose up

install:
	docker-compose run --rm frontend npm i
	docker-compose run --rm composer

docker-build:
	docker-compose build

run:
	docker-compose up -d db phpmyadmin
	docker-compose run -d --name ecamp3-backend-lean  --service-ports --entrypoint "./docker-run.sh" backend 
	docker-compose run    --name ecamp3-frontend-lean --service-ports frontend npm run serve