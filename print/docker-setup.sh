#!/bin/bash
set -euo pipefail

mkdir -p /home/node/.cache/node-gyp/18.6.0

usermod -u $USER_ID node
groupmod -g $USER_ID node
chown -R node:node /home/node/.cache

exec su node -c "npm ci && npm run dev"
