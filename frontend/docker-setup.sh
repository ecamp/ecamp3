#!/bin/bash
set -euo pipefail

BASEDIR=$(dirname "$0")
PDF_DIST=$BASEDIR"/src/pdf"

if [ ! -f "$PDF_DIST/pdf.mjs" ] || [ ! -f "$PDF_DIST/prepareInMainThread.mjs" ]; then
    # Copy dummy versions of the pdf build outputs, to make sure there is always something to import
    cp "$PDF_DIST/pdf.mjs.dist" "$PDF_DIST/pdf.mjs"
    cp "$PDF_DIST/prepareInMainThread.mjs.dist" "$PDF_DIST/prepareInMainThread.mjs"
fi

if [ "$CI" = 'true' ] ; then
  npm ci --verbose
  npm run build
  npm run preview
else
  npm install
  npm run dev
fi
