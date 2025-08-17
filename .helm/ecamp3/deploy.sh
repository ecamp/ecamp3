#!/bin/sh

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR/../..")

if [ -z "${NAME:-}" ]; then
  echo "Please specify the NAME"
  exit 1
fi

cd "$SCRIPT_DIR"

helmfile write-values --environment default --output-file-template values.yaml

HELM_TIMEOUT="${HELM_TIMEOUT:-5m0s}"

if [ "${1:-}" = "deploy" ]; then
  helm upgrade --install ecamp3-$NAME \
    $SCRIPT_DIR \
    --values $SCRIPT_DIR/values.yaml \
    --timeout "$HELM_TIMEOUT"
  exit 0
fi

if [ "${1:-}" = "diff" ]; then
  helm template --no-hooks --skip-tests ecamp3-$NAME \
    $SCRIPT_DIR \
    --values $SCRIPT_DIR/values.yaml | kubectl diff -f -
  exit 0
fi
