# build stage
FROM node:lts-alpine@sha256:60ef0bed1dc2ec835cfe3c4226d074fdfaba571fd619c280474cc04e93f0ec5b AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:74694f2de64c44787a81f0554aa45b281e468c0c58b8665fafceda624d31e556 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
