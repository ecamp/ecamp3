# build stage
FROM node:lts-alpine@sha256:c3126f821c9df618d0f734ad1babd9b45c73c1fa2b2e18ea709dff00a3283cea AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:0d577377e6908742dfcb91cf3fb361e1af49d1659957bf464bc808f6fe335d9a as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
