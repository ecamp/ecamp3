# build stage
FROM node:20.9.0 AS build-stage
ARG SENTRY_AUTH_TOKEN
ARG SENTRY_ORG
ARG SENTRY_PRINT_PROJECT
ARG SENTRY_RELEASE_NAME

COPY common /common

WORKDIR /app

COPY print/package*.json ./

ENV SENTRY_AUTH_TOKEN=$SENTRY_AUTH_TOKEN
ENV SENTRY_ORG=$SENTRY_ORG
ENV SENTRY_PRINT_PROJECT=$SENTRY_PRINT_PROJECT
ENV SENTRY_RELEASE_NAME=$SENTRY_RELEASE_NAME

RUN npm ci

COPY print .
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN npm run build

# production stage
FROM node:20.9.0 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY --from=build-stage /app/server-middleware ./server-middleware
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
