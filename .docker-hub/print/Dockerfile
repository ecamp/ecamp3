# build stage
FROM node:14.15.4@sha256:79c6e16f88fb5539b9585e6beaee8c6f9da49aea5073f59148d2e41a277b056b AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.15.4@sha256:79c6e16f88fb5539b9585e6beaee8c6f9da49aea5073f59148d2e41a277b056b AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
