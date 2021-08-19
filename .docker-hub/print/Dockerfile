# build stage
FROM node:16.7.0@sha256:d75b1663c035a5e9ed48ad54041aff3067d6a66104d32d07901314e573393d2a AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.7.0@sha256:d75b1663c035a5e9ed48ad54041aff3067d6a66104d32d07901314e573393d2a AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
