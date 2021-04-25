# build stage
FROM node:lts-alpine@sha256:ed51af876dd7932ce5c1e3b16c2e83a3f58419d824e366de1f7b00f40c848c40 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:e015192ec74937149dce3aa1feb8af016b7cce3a2896246b623cfd55c14939a6 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
