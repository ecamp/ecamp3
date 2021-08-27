# build stage
FROM node:16.8.0@sha256:7dd991e0ca991c3d37c3e660b7f35d134136f50fd5922dd8c92879d62793c58e AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.8.0@sha256:7dd991e0ca991c3d37c3e660b7f35d134136f50fd5922dd8c92879d62793c58e AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
