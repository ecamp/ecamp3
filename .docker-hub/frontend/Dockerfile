# build stage
FROM node:lts-alpine@sha256:5e96a11d395e8fd0652bbbc35cf4da16f38cf9559ec859f364aa93073bc758dd AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:2012644549052fa07c43b0d19f320c871a25e105d0b23e33645e4f1bcf8fcd97 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
