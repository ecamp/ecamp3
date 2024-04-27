#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR

# to debug: --dry-run --debug
helm dep build && helm upgrade --install ops-dashboard --namespace=ops-dashboard --create-namespace $SCRIPT_DIR --values $SCRIPT_DIR/values.yaml --values $SCRIPT_DIR/values.access.yaml
