# build stage
FROM node:16.6.2@sha256:c4c9c805c1fbc6fe580343ca13469540e8d3dc1991b3a487c3e32fec22a7c755 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.6.2@sha256:c4c9c805c1fbc6fe580343ca13469540e8d3dc1991b3a487c3e32fec22a7c755 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
