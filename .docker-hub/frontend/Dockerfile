# build stage
FROM node:lts-alpine@sha256:3bca55259ada636e5fee8f2836aba7fa01fed7afd0652e12773ad44af95868b9 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:b92d3b942c8b84da889ac3dc6e83bd20ffb8cd2d8298eba92c8b0bf88d52f03e as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
