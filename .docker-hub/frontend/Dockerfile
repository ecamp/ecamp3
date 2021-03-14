# build stage
FROM node:lts-alpine@sha256:a75f7cc536062f9266f602d49047bc249826581406f8bc5a6605c76f9ed18e98 AS build-stage

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
