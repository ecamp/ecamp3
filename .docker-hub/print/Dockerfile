# build stage
FROM node:14.17.0@sha256:af9879e7473d347048c5d5919aa9775f27c33d92e4d58058ffdc08247f4bd902 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:14.17.0@sha256:af9879e7473d347048c5d5919aa9775f27c33d92e4d58058ffdc08247f4bd902 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
