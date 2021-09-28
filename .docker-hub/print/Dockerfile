# build stage
FROM node:16.10.0@sha256:cb1324e419531879e6c2fa6f33ba6d3f71c5ce3e134529b52a9804712be85ce3 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.10.0@sha256:cb1324e419531879e6c2fa6f33ba6d3f71c5ce3e134529b52a9804712be85ce3 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
