#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
# to debug: --dry-run --debug
helm upgrade --install ecamp3-logging --namespace=ecamp3-logging --create-namespace $SCRIPT_DIR
