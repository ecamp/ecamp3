# build stage
FROM node:16.15.1@sha256:a13d2d2aec7f0dae18a52ca4d38b592e45a45cc4456ffab82e5ff10d8a53d042 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.15.1@sha256:a13d2d2aec7f0dae18a52ca4d38b592e45a45cc4456ffab82e5ff10d8a53d042 AS production-stage
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
