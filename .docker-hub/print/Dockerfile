# build stage
FROM node:16.5.0@sha256:8229a1f3580d32fa18b2304fa23df6e9e3d53fdb958fd8ffe812ca7a0a26bb69 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.5.0@sha256:8229a1f3580d32fa18b2304fa23df6e9e3d53fdb958fd8ffe812ca7a0a26bb69 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
