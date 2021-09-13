# build stage
FROM node:16.9.0@sha256:94fc8b0a24f03336536ee099a1a70afe9cfe7e71462b65aae79c17426ea4a372 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.9.0@sha256:94fc8b0a24f03336536ee099a1a70afe9cfe7e71462b65aae79c17426ea4a372 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
