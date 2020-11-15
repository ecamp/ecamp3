# build stage
FROM node:12.18 AS build-stage

WORKDIR /common
COPY common .

WORKDIR /app
COPY print .

RUN npm install
RUN npm run build

# production stage
FROM node:12.18 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
