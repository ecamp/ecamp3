# build stage
FROM node:16.10.0@sha256:c33a8221330d87a2113fb6cdbb8fbb243592c62f89de923e9ef5bb8b627c2b9d AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.10.0@sha256:c33a8221330d87a2113fb6cdbb8fbb243592c62f89de923e9ef5bb8b627c2b9d AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
