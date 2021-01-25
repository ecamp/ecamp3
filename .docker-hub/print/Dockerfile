# build stage
FROM node:14.15.4@sha256:c8000514cb7d4cf1fb3ea2e7f5d397512392b0bd70dc2c94ba89c147a3ba8414 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.15.4@sha256:c8000514cb7d4cf1fb3ea2e7f5d397512392b0bd70dc2c94ba89c147a3ba8414 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
