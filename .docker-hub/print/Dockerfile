# build stage
FROM node:16.15.0@sha256:1817bb941c9a30fe2a6d75ff8675a8f6def408efe3d3ff43dbb006e2b534fa14 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.15.0@sha256:1817bb941c9a30fe2a6d75ff8675a8f6def408efe3d3ff43dbb006e2b534fa14 AS production-stage
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
