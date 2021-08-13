# build stage
FROM node:lts-alpine@sha256:b8d48b515e3049d4b7e9ced6cedbe223c3bc4a3d0fd02332448f3cdb000faee1 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:bac218df22fef66a173cfa65d0dfa0acca80a3a39df41665be742596977b6c7c as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
