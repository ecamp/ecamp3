# build stage
FROM node:22.10.0 AS build-stage
ARG SENTRY_AUTH_TOKEN
ARG SENTRY_ORG
ARG SENTRY_PRINT_PROJECT
ARG SENTRY_RELEASE_NAME

COPY common /common

WORKDIR /app

COPY print/package*.json ./

ENV NUXT_SENTRY_AUTH_TOKEN=$SENTRY_AUTH_TOKEN
ENV NUXT_SENTRY_ORG=$SENTRY_ORG
ENV NUXT_SENTRY_PROJECT=$SENTRY_PRINT_PROJECT
ENV NUXT_SENTRY_RELEASE_NAME=$SENTRY_RELEASE_NAME

RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:22.10.0 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/.output ./.output

ENV NITRO_HOST 0.0.0.0
ENV NITRO_PORT 80

EXPOSE 80
CMD node ./.output/server/index.mjs
