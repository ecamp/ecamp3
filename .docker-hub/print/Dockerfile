# build stage
FROM node:16.13.0@sha256:683b8ea4ebc033a0f9060501fc31c1481d3f7232cc032851abbd8cc8d91fdff7 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.13.0@sha256:683b8ea4ebc033a0f9060501fc31c1481d3f7232cc032851abbd8cc8d91fdff7 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
