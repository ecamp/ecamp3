# build stage
FROM node:17.7.2@sha256:720d77136dc06bbdac28ef5cd13c385e40a2f1bfaaf7340180fc66820cc184e3 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:17.7.2@sha256:720d77136dc06bbdac28ef5cd13c385e40a2f1bfaaf7340180fc66820cc184e3 AS production-stage
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
