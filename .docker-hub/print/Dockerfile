# build stage
FROM node:14.15.4@sha256:04a33dac55af8d3170bffc91ca31fe8000b96ae1bab1a090deb920ca2ca2a38e AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.15.4@sha256:04a33dac55af8d3170bffc91ca31fe8000b96ae1bab1a090deb920ca2ca2a38e AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
