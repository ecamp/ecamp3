# build stage
FROM node:18.10.0@sha256:9d8a6466c6385e05f62f8ccf173e80209efb0ff4438f321f09ddf552b05af3ba AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN mkdir -p /home/node/.cache/node-gyp/18.7.0 && npm ci

COPY print .
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN npm run build

# production stage
FROM node:18.10.0@sha256:9d8a6466c6385e05f62f8ccf173e80209efb0ff4438f321f09ddf552b05af3ba AS production-stage
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
