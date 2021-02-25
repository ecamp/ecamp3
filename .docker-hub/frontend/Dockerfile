# build stage
FROM node:lts-alpine@sha256:07b040b2feb82b5384c2449630a4591138485476d20f7caf96ac8a6725056381 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:34039e81cf9de5f7f92f6280701e92cd51b85fb6b7170c41f6bf8920fcc79f8e as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
