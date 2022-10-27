# build stage
FROM node:lts-alpine@sha256:842770ef089fcb7c3bc40fcbf2b1f80fd2361558d4950f03f24444767265e613 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 && npm ci && apk del native-deps
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:d24e098389beed466ea300d5473cdda939bf6e91a93873d0fd1dd18e138c2f13 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
