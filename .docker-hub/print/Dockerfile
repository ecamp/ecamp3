# build stage
FROM node:14.16.0@sha256:f6b9ff4caca9d4f0a331a882e560df242eb332b7bbbed2f426784de208fcd7bd AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.16.0@sha256:f6b9ff4caca9d4f0a331a882e560df242eb332b7bbbed2f426784de208fcd7bd AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
