# build stage
FROM node:16.7.0@sha256:48724c60fe610e1295cc5f0aa8d9f2a87a4daf4a66d0089f910fcaf5c7e8d912 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.7.0@sha256:48724c60fe610e1295cc5f0aa8d9f2a87a4daf4a66d0089f910fcaf5c7e8d912 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
