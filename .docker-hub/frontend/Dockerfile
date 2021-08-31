# build stage
FROM node:lts-alpine@sha256:76f47d2944d54aa8f80da2c7a633473f528b5e625b92d6c30afe8620d5d1869a AS build-stage

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
