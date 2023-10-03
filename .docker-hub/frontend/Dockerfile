# pdf build stage
FROM node:lts-alpine AS pdf-build-stage

COPY common /common

# Build the pdf module
COPY pdf /pdf
WORKDIR /pdf
RUN npm ci && npm run build

# frontend build stage
FROM node:lts-alpine AS frontend-build-stage
ARG SENTRY_AUTH_TOKEN
ARG SENTRY_ORG
ARG SENTRY_FRONTEND_PROJECT
ARG SENTRY_RELEASE_NAME

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
COPY frontend/public ./public
COPY frontend/scripts ./scripts
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 git && npm ci && apk del native-deps
COPY frontend .
COPY --from=pdf-build-stage /pdf/dist ./src/pdf
COPY --from=pdf-build-stage /pdf/src/assets/fonts/Inter ./src/assets/fonts/Inter

ENV SENTRY_AUTH_TOKEN=$SENTRY_AUTH_TOKEN
ENV SENTRY_ORG=$SENTRY_ORG
ENV SENTRY_FRONTEND_PROJECT=$SENTRY_FRONTEND_PROJECT
ENV SENTRY_RELEASE_NAME=$SENTRY_RELEASE_NAME

RUN npm run build

# production stage
FROM nginx:stable-alpine as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=frontend-build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
COPY .docker-hub/frontend/docker-entrypoint.d/* /docker-entrypoint.d/
RUN chmod -R +x /docker-entrypoint.d/
