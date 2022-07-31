# build stage
FROM node:16.16.0@sha256:4e85818bd0d023d4f9025730dc0640d3d8269e3d1a84ce6365eca8fbad7a3ee9 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.16.0@sha256:4e85818bd0d023d4f9025730dc0640d3d8269e3d1a84ce6365eca8fbad7a3ee9 AS production-stage
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
