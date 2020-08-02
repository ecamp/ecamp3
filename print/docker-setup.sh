#!/bin/bash

BASEDIR=$(dirname "$0")
ENV_FILE=$BASEDIR"/static/environment.js"

if [ ! -f "$ENV_FILE" ]; then
    cp $BASEDIR/static/environment.docker.dist "$ENV_FILE"
fi

npm i

npm run dev