# build stage
FROM node:16.13.0@sha256:47c0a92062d7a413fd5c15f1edcdc3e8988bcce1657ec6fe0d0c660b9258dfea AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.13.0@sha256:47c0a92062d7a413fd5c15f1edcdc3e8988bcce1657ec6fe0d0c660b9258dfea AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
