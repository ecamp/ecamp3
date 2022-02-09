# build stage
FROM node:16.13.2@sha256:2033f4cc18f9d8b5d0baa7f276aaeffd202e1a2c6fe9af408af05a34fe68cbfb AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.13.2@sha256:2033f4cc18f9d8b5d0baa7f276aaeffd202e1a2c6fe9af408af05a34fe68cbfb AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY --from=build-stage /app/server-middleware ./server-middleware
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
