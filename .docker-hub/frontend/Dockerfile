# build stage
FROM node:lts-alpine AS build-stage

WORKDIR /common
COPY common .

WORKDIR /app
COPY frontend/package*.json ./
RUN npm install
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
