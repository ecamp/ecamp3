# build stage
FROM node:16.13.1@sha256:32605ead97ed57bd39a8a7b0e919240e1a3218974dfc6965e61b54a801753131 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.13.1@sha256:32605ead97ed57bd39a8a7b0e919240e1a3218974dfc6965e61b54a801753131 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
