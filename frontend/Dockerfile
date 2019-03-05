FROM node:11.10.1-alpine AS npm-install
RUN mkdir -p /app
WORKDIR /app
COPY frontend/package.json ./package.json
COPY frontend/package-lock.json ./package-lock.json
RUN npm install


FROM node:11.10.1-alpine
COPY --from=npm-install /app /app
WORKDIR /app
ENTRYPOINT npm run serve
