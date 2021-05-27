# build stage
FROM node:14.17.0@sha256:317c1f6bfcd0037c593cf0e1c4f6c844a81441b95603365aabb9ac1cd3bd5aef AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.0@sha256:317c1f6bfcd0037c593cf0e1c4f6c844a81441b95603365aabb9ac1cd3bd5aef AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
