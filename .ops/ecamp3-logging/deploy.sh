#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR

if [ $# -ne 1 ]; then
    echo "Usage: $0 <prod|dev>"
    echo "(or other environments)"
    exit 1
fi

# to debug: --dry-run --debug
helm dep build
helm upgrade --install ecamp3-logging \
    --namespace=ecamp3-logging \
    --create-namespace \
    $SCRIPT_DIR \
    --values $SCRIPT_DIR/values.yaml \
    --values $SCRIPT_DIR/values-$1.yaml
