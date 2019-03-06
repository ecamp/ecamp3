FROM node:11.10.1-alpine AS npm-install
RUN mkdir -p /app
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
ENTRYPOINT npm run serve
