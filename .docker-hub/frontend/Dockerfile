# build stage
FROM node:lts-alpine@sha256:afa007fd9ed55e78a01998d79197c5f2d586b806ed031a595f4fbb25db03a607 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:98a1e37840fdf90f57df595dae8e27a198278170323744e13464b7f3a927562e as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
