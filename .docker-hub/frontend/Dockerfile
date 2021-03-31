# build stage
FROM node:lts-alpine@sha256:8bd6100693a93cca2a3c3b1d8dfbcb7434d4b1731c625204966a87ea2efb36bc AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:6386f5445b60d9b48efdc9034111b7ce62098e61309e5d3871542f64722e3211 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
