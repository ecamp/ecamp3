# build stage
FROM node:16.6.0@sha256:9430fd769545661774788faeea0abcfe71a7f07f9da10b803552f8c7081edf7b AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.6.0@sha256:9430fd769545661774788faeea0abcfe71a7f07f9da10b803552f8c7081edf7b AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
