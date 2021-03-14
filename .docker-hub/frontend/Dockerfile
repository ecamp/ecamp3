# build stage
FROM node:lts-alpine@sha256:76badf0d0284ad838536d49d8a804988b4985fc6bc7242dfff4f8216c851438b AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:5aa813bef3232f2122c5ba042246d0f52baa6db95a05e8fa6f6c5ace3b410ebc as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
