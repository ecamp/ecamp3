# build stage
FROM node:16.6.2@sha256:1a658b891340da03322eec3d95d48de319a3b8dda0f1cc4cf7aa46dd20e4a198 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.6.2@sha256:1a658b891340da03322eec3d95d48de319a3b8dda0f1cc4cf7aa46dd20e4a198 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
