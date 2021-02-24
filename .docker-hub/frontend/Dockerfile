# build stage
FROM node:lts-alpine@sha256:07b040b2feb82b5384c2449630a4591138485476d20f7caf96ac8a6725056381 AS build-stage

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
