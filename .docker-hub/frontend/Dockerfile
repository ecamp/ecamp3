# build stage
FROM node:lts-alpine@sha256:cdc1f725e56aae1266069bfda5a97a71c9f4d0e5e2675c6b80a4cd37e8fefba4 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:978106c2dc595fe4f247a1d1f8290b92039025a08eb3300d31a4ea8691d54e83 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
