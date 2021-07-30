# build stage
FROM node:lts-alpine@sha256:726642e5eb66aed9cd41a09bd6107954f8feb2fe4c1cd5bcf45149ab0814d577 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:7b801536219e8b3a5dcaf0c5a34c4d4d0514e648905c83105d98c56740eb4f07 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
