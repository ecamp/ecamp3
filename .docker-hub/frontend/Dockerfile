# build stage
FROM node:lts-alpine@sha256:61f5ce614667fc85b4ff48b4f4817073da6844c2da67dd15b15bc2b7175823c4 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:7ae8e5c3080f6012f8dc719e2308e60e015fcfa281c3b12bf95614bd8b6911d6 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
