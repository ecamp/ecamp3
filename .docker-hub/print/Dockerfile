# build stage
FROM node:16.3.0@sha256:e36cf1bb8719551220ba8c3ee1583881e79ad040803570e0849b00b8fe009153 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.3.0@sha256:e36cf1bb8719551220ba8c3ee1583881e79ad040803570e0849b00b8fe009153 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
