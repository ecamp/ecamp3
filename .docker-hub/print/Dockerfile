# build stage
FROM node:16.10.0@sha256:543c034446e58fb3991842e22db7a404f5feb1050cf6126cb551e03cae0e7ac1 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.10.0@sha256:543c034446e58fb3991842e22db7a404f5feb1050cf6126cb551e03cae0e7ac1 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
