# build stage
FROM node:lts-alpine@sha256:2175727cef5cad4020cb77c8c101d56ed41d44fbe9b1157c54f820e3d345eab1 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:c27238c20255fafe122db4c51dddf26544adae1bc397d978aa36aa2a092cb739 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
