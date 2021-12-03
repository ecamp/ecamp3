# build stage
FROM node:16.13.1@sha256:e4ce479ba2d4d625889ed49e23fa3a08962a4a9e6ab20d5bdf05aa011b0ad86a AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.13.1@sha256:e4ce479ba2d4d625889ed49e23fa3a08962a4a9e6ab20d5bdf05aa011b0ad86a AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
