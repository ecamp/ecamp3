# build stage
FROM node:16.15.1@sha256:6155ff062c403e99c1da7c317710c5c838c1e060f526d98baea6ee921ca61729 AS build-stage

COPY common /common

WORKDIR /app

COPY print/package*.json ./
RUN npm ci

COPY print .
RUN npm run build

# production stage
FROM node:16.15.1@sha256:6155ff062c403e99c1da7c317710c5c838c1e060f526d98baea6ee921ca61729 AS production-stage
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
