# build stage
FROM node:lts-alpine@sha256:c1685b20cb00ab488dd7c4371973a6f62fc831b765a2b00ea2500466f2a8e74e AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:b79e09601c46804c4be674e2674d5b4b2f497a51141bb3fc08698fbef9058648 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
COPY .docker-hub/frontend/docker-entrypoint.d/* /docker-entrypoint.d/
RUN chmod -R +x /docker-entrypoint.d/
