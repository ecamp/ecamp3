# build stage
FROM node:14.17.2@sha256:d8780c3e27fd9e7d2d702b230d435ad6b655051f84aeac6e2c355141078c23bc AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.2@sha256:d8780c3e27fd9e7d2d702b230d435ad6b655051f84aeac6e2c355141078c23bc AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
