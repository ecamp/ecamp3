# build stage
FROM node:16.12.0@sha256:7af54841a6828288a57747564b235af2c4ab86bf3c3591bdf0fc6db08b8219f3 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.12.0@sha256:7af54841a6828288a57747564b235af2c4ab86bf3c3591bdf0fc6db08b8219f3 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
