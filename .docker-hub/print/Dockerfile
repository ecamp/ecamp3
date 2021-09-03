# build stage
FROM node:16.8.0@sha256:62a949ec910c39fa22f46c6a126e3851fd707def8af56a8b322b2c5a877715bc AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.8.0@sha256:62a949ec910c39fa22f46c6a126e3851fd707def8af56a8b322b2c5a877715bc AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
