# frontend build stage
FROM node:26.13.1-alpine AS frontend-build-stage
ARG SENTRY_AUTH_TOKEN
ARG SENTRY_ORG
ARG SENTRY_FRONTEND_PROJECT
ARG SENTRY_RELEASE_NAME

WORKDIR /app
COPY frontend/package*.json frontend/.npmrc ./
COPY frontend/public ./public
COPY frontend/scripts ./scripts
COPY frontend/patches ./patches
# install and uninstall the native dependencies in one single docker RUN instruction,
# so they do not increase the image layer size
RUN apk --no-cache add --virtual native-deps g++ make python3 git && npm ci && apk del native-deps
COPY frontend .

ENV SENTRY_AUTH_TOKEN=$SENTRY_AUTH_TOKEN
ENV SENTRY_ORG=$SENTRY_ORG
ENV SENTRY_FRONTEND_PROJECT=$SENTRY_FRONTEND_PROJECT
ENV SENTRY_RELEASE_NAME=$SENTRY_RELEASE_NAME

RUN npm run build

# production stage
FROM ghcr.io/nginx/nginx-unprivileged:1.31.9-alpine3.24 AS production-stage
USER root
RUN mkdir /app
WORKDIR /app
COPY --from=frontend-build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
RUN chgrp -R 0 /var/log/nginx/ && \
    chmod -R -w /app && \
    chmod -R g+w /var/log/nginx/ /app/index.html

# From https://github.com/nginx/docker-nginx-unprivileged/blob/872c2c0617f10eecc31b7826168a759f24dc02d7/Dockerfile-alpine.template#L7
USER 101
