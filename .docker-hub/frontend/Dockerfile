# build stage
FROM node:lts-alpine@sha256:726642e5eb66aed9cd41a09bd6107954f8feb2fe4c1cd5bcf45149ab0814d577 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:f527c86fcced1cc71cb254dd341094ef670cebe1d0daa46f1bbe0a6fd6e3a592 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
