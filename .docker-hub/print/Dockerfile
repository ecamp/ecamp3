# build stage
FROM node:14.17.3@sha256:976c9107158a1c85ab0702aec5b1d56bbb85de493ca50794e545a0271421e028 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.3@sha256:976c9107158a1c85ab0702aec5b1d56bbb85de493ca50794e545a0271421e028 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
