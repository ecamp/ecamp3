#!/bin/sh
set -e

if [ -n $VERSION ]; then
  sed -i "s|<script src=\"/environment.js\"|<script src=\"/environment.js?version=$VERSION\"|" /app/index.html
fi
