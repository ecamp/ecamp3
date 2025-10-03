#!/bin/bash
set -euo pipefail

BASEDIR=$(dirname "$0")
PDF_DIST=$BASEDIR"/src/pdf"

if [ ! -f "$PDF_DIST/pdf.js" ] || [ ! -f "$PDF_DIST/prepareInMainThread.js" ]; then
    # Copy dummy versions of the pdf build outputs, to make sure there is always something to import
    cp "$PDF_DIST/pdf.js.dist" "$PDF_DIST/pdf.js"
    cp "$PDF_DIST/prepareInMainThread.js.dist" "$PDF_DIST/prepareInMainThread.js"
fi

if [ "$CI" = 'true' ] ; then
  npm ci --verbose
  npm run build
  npm run preview
else
  npm install
  npm run dev
fi
