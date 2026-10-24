#!/bin/sh
set -e

DIGEST=$(sha256sum /app/environment.js | cut -d ' ' -f 1)
sed -i "s|<script src=\"/environment.js\"|<script src=\"/environment.js?version=$DIGEST\"|" /app/index.html
