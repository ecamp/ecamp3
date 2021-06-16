# build stage
FROM node:14.17.1@sha256:52fe2e3604ca3d7d0bc017c0d60df5baaf54c3e261bab8334b0657f923092523 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.1@sha256:52fe2e3604ca3d7d0bc017c0d60df5baaf54c3e261bab8334b0657f923092523 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
