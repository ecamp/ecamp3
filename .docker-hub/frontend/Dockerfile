# build stage
FROM node:lts-alpine@sha256:963724bb905330e73a72511e1ca2ae337e49ccb7636875777cd11324430c690e AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:eedbaccc242692c64b5996a363baaa9d9db65a8fc123483af13c640e5dee9b47 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
