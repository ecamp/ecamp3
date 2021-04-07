# build stage
FROM node:lts-alpine@sha256:aeb57f10e71d0d7c8e8b9821faf1793e615483bee5fc7dce90f8c82731f80827 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:ec3f6e4587a2053f406b1b4e027c260e82e4e32c256c04e798b52cb4f877e688 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
