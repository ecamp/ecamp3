# build stage
FROM node:lts-alpine@sha256:cdc1f725e56aae1266069bfda5a97a71c9f4d0e5e2675c6b80a4cd37e8fefba4 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:1173fbfbbffd6e8a975a9a5a31edd4409b78d11fdc5e67985e13ea2dea915290 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
