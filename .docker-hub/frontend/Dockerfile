# build stage
FROM node:lts-alpine@sha256:1908564153449b1c46b329e6ce2307e226bc566294f822f11c5a8bcef4eeaad7 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:6eeb2db7c28b1c68351fdb1464887690b6bf7a99f0c8db9d2baa1e7b4aad8d96 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
