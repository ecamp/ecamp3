#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR
# to debug: --dry-run --debug
helm dep build
helm upgrade --install ecamp3-logging \
    --namespace=ecamp3-logging \
    --create-namespace \
    $SCRIPT_DIR \ 
    --values $SCRIPT_DIR/values.yaml
