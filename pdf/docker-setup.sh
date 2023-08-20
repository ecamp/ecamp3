#!/bin/bash
set -euo pipefail

npm ci --verbose

if [ "$CI" = 'true' ] ; then
  npm run build
else
  npm run watch
fi
