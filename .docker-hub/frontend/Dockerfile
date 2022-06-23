# build stage
FROM node:lts-alpine@sha256:c785e617c8d7015190c0d41af52cc69be8a16e3d9eb7cb21f0bb58bcfca14d6b AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:0a88a14a264f46562e2d1f318fbf0606bc87e72727528b51613a5e96f483a0f6 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
