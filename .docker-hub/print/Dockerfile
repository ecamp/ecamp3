# build stage
FROM node:16.13.1@sha256:c2a7504b9d89184d0275169483e772b23d629dc8df7a1545f441d2db05ade4dc AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.13.1@sha256:c2a7504b9d89184d0275169483e772b23d629dc8df7a1545f441d2db05ade4dc AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
