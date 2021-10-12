# build stage
FROM node:16.11.0@sha256:aa1e997e30f6616f0517f6968982e3cd73bff84529d852cbdf0521592bc582aa AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.11.0@sha256:aa1e997e30f6616f0517f6968982e3cd73bff84529d852cbdf0521592bc582aa AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
