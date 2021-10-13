# build stage
FROM node:16.11.1@sha256:fec9ab31c9d46ad952abed8119b71e01e995975bd7b580f07b249e803d306dbe AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.11.1@sha256:fec9ab31c9d46ad952abed8119b71e01e995975bd7b580f07b249e803d306dbe AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
