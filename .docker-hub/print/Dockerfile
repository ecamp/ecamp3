# build stage
FROM node:14.16.1@sha256:e77e35d3b873500c10ce8969fe2ce5e0901516f77c8365d029c4b42b22ee4bac AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.16.1@sha256:e77e35d3b873500c10ce8969fe2ce5e0901516f77c8365d029c4b42b22ee4bac AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
