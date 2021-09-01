# build stage
FROM node:lts-alpine@sha256:2e2e7d1a303bd8541d939e05f92eae87fb2df60265be9626d91b623120852986 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:1173fbfbbffd6e8a975a9a5a31edd4409b78d11fdc5e67985e13ea2dea915290 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
