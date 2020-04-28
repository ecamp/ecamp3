#!/bin/sh

npm install

ENV_FILE="public/environment.js"
if [ ! -f "$ENV_FILE" ]; then
    cp public/environment.docker.dist "$ENV_FILE"
fi

cp -r node_modules/* /node_modules-copy-to-host

npm run serve
