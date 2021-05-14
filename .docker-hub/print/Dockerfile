# build stage
FROM node:14.17.0@sha256:9025a77b2f37fcda3bbd367587367a9f2251d16a756ed544550b8a571e16a653 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.0@sha256:9025a77b2f37fcda3bbd367587367a9f2251d16a756ed544550b8a571e16a653 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
