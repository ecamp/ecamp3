#!/bin/bash
set -euo pipefail

if [ "$CI" = 'true' ] ; then
  npm ci --verbose
  npm run build
  npm run preview
else
  npm install
  npm run dev
fi
