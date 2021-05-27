# build stage
FROM node:lts-alpine@sha256:dc7cbd288ba1e4a67443f3ed756a042dfeba26349b873e6fd5f1b0909237bdc1 AS build-stage

COPY common /common

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend .
RUN npm run build

# production stage
FROM nginx:stable-alpine@sha256:4c945e23f40334b2f0e8d6e5040a0ea06fc5c2e0cb355d1af0ae8ba0cdf80ea8 as production-stage
RUN mkdir /app
WORKDIR /app
COPY --from=build-stage /app/dist /app
COPY .docker-hub/frontend/nginx.conf /etc/nginx/nginx.conf
