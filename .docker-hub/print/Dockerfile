# build stage
FROM node:16.9.1@sha256:8710efa3fc8be1cbce674c646c1c64a993a70ca767bd56bae81e232855261973 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.9.1@sha256:8710efa3fc8be1cbce674c646c1c64a993a70ca767bd56bae81e232855261973 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
