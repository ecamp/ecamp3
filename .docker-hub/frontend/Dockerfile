# build stage
FROM node:lts-alpine@sha256:b4d1e48e89227fbbc88053c43189813075a3602b60242810d56472e075140890 AS build-stage

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
