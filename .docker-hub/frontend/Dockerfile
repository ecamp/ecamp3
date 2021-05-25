# build stage
FROM node:lts-alpine@sha256:3689ad4435a413342ccc352170ad0f77433b41173af7fe4c0076f0c9792642cb AS build-stage

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
