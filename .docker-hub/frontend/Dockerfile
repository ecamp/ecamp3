# build stage
FROM node:lts-alpine@sha256:71a9ab5b02d8ddbcb56dab75d6d58720a2bb7536b4c72688b6443a22f2718676 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:1a08bb3e8581306cc53cc55beede88fcb242cd4faead0f31d08801c621528166 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
