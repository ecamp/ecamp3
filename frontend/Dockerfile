FROM node:12.13.1-alpine

WORKDIR /app

# Installed for now to be able to use dependencies directly from GitHub
RUN apk add --no-cache git

ENTRYPOINT sh docker-entrypoint.sh
