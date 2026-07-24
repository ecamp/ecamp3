#!/bin/bash
set -euo pipefail

BASEDIR=$(dirname "$0")

if [ "$CI" = 'true' ] ; then
  npm ci --verbose
  npm run build
  npm run preview
elif [ "${PREVIEW:-}" = 'true' ] ; then
  npm install
  npm run build -- --watch &
  # `vite preview` exits if dist/ is missing, so wait for the first build to emit it.
  until [ -f dist/index.html ] ; do sleep 2 ; done
  npm run preview
else
  npm install
  npm run dev
fi
