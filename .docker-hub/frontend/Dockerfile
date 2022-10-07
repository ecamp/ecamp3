# build stage
FROM node:lts-alpine@sha256:4d68856f48be7c73cd83ba8af3b6bae98f4679e14d1ff49e164625ae8831533a AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:addd3bf05ec3c69ef3e8f0021ce1ca98e0eb21117b97ab8b64127e3ff6e444ec as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
