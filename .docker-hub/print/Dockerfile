# build stage
FROM node:14.16.1@sha256:b8d8e6a133c62ea8d1667c50e80551d5c84c553b89873a6a5a92cfe00e449dbf AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.16.1@sha256:b8d8e6a133c62ea8d1667c50e80551d5c84c553b89873a6a5a92cfe00e449dbf AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
