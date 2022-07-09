# build stage
FROM node:16.16.0@sha256:8951351b7c6a2f8ff9ec25eccc087d37a8aeccf9bf911888ff13c76223467466 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.16.0@sha256:8951351b7c6a2f8ff9ec25eccc087d37a8aeccf9bf911888ff13c76223467466 AS production-stage
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
