# build stage
FROM node:18.14.0@sha256:355dcfa2477065f2281f3887947f1abf973f562b9f1a26c81b6f7aee54650b46 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN mkdir -p /home/node/.cache/node-gyp/18.7.0 && npm ci

COPY print .
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN npm run build

# production stage
FROM node:18.14.0@sha256:355dcfa2477065f2281f3887947f1abf973f562b9f1a26c81b6f7aee54650b46 AS production-stage
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
