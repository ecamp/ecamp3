# build stage
FROM node:16.11.0@sha256:7923a071fc946f0981deb17b82d99468541f15fa5d07e94318e7a1ec10f690f4 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.11.0@sha256:7923a071fc946f0981deb17b82d99468541f15fa5d07e94318e7a1ec10f690f4 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
