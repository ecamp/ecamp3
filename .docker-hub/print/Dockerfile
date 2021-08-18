# build stage
FROM node:16.6.2@sha256:30f6ad211b10774a0da8ca90b9f8268ee7530e33ad435ddf6deb2bb7cb1e7940 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.6.2@sha256:30f6ad211b10774a0da8ca90b9f8268ee7530e33ad435ddf6deb2bb7cb1e7940 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
