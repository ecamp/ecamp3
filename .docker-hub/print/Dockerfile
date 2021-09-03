# build stage
FROM node:16.8.0@sha256:84b0f5a6d4b14c2c7890799f8c40d7c874836f5232e8d21fac661f5775b40889 AS build-stage

COPY common /common

WORKDIR /app
COPY print .

RUN npm ci
RUN npm run build

# production stage
FROM node:16.8.0@sha256:84b0f5a6d4b14c2c7890799f8c40d7c874836f5232e8d21fac661f5775b40889 AS production-stage
WORKDIR /app

COPY --from=build-stage /app/node_modules ./node_modules
COPY --from=build-stage /app/.nuxt ./.nuxt
COPY --from=build-stage /app/static ./static
COPY print/nuxt.config.js .

ENV NUXT_HOST 0.0.0.0
ENV NUXT_PORT 80

EXPOSE 80
CMD ./node_modules/.bin/nuxt start
