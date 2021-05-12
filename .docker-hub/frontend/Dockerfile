# build stage
FROM node:lts-alpine@sha256:d1a10d5865ab05430703f75e5a2a6897c1106a6f264d0317fbd785e24d4e8b25 AS build-stage

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
