# build stage
FROM node:18.9.1@sha256:d6ed353d022f6313aa7c3f3df69f3a216f1c9f8c3374502eb5e6c45088ce68e8 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN mkdir -p /home/node/.cache/node-gyp/18.7.0 && npm ci

COPY print .
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN npm run build

# production stage
FROM node:18.9.1@sha256:d6ed353d022f6313aa7c3f3df69f3a216f1c9f8c3374502eb5e6c45088ce68e8 AS production-stage
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
