# build stage
FROM node:lts-alpine@sha256:8263de4a04679fabfdbda4e3a18de44d303ec5ba4dd3dafaf5fdc69af7164fd6 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:4ca0ee2d0f82bd18879a5d6f51365db49d7fcfd4768c113820c387a676ce7ac5 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
