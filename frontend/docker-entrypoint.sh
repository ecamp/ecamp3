#!/bin/sh

npm install

ENV_FILE=".env.local"
if [ ! -f "ENV_FILE" ]; then
    cp .env.docker.dist "$ENV_FILE"
fi

cp -r node_modules/* /node_modules-copy-to-host

npm run serve
