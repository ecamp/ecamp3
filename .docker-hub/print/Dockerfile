# build stage
FROM node:14.17.3@sha256:ddb46bba1d217197a8bdc9c98a22026f2bbf8e609a7e758af0c30a83619c11fe AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.3@sha256:ddb46bba1d217197a8bdc9c98a22026f2bbf8e609a7e758af0c30a83619c11fe AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
