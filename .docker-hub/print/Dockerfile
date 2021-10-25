# build stage
FROM node:16.12.0@sha256:922d9e469a2fcd2554acd5c309c1d5a6306cb5483fc5a45b14c8d1bb775c5ecb AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.12.0@sha256:922d9e469a2fcd2554acd5c309c1d5a6306cb5483fc5a45b14c8d1bb775c5ecb AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
