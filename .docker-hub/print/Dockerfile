# build stage
FROM node:16.9.0@sha256:a9b63836b98ed09b0c7a6b6a8c9ae0c2fbc0bda2de0cebf4019aace805875e0f AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.9.0@sha256:a9b63836b98ed09b0c7a6b6a8c9ae0c2fbc0bda2de0cebf4019aace805875e0f AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
