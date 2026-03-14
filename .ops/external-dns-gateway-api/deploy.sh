#!/bin/sh

set -ea

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR

if [ -f $SCRIPT_DIR/.env ]; then
  . $SCRIPT_DIR/.env
fi

helmfile deps
helmfile write-values --environment default --output-file-template values.yaml

if [ "$1" = "deploy" ]; then
  # to debug: --dry-run --debug
  helm upgrade --install external-dns-gateway-api \
      --namespace external-dns-gateway-api \
      --create-namespace \
      $SCRIPT_DIR \
      --values $SCRIPT_DIR/values.yaml
  exit 0
fi

if [ "$1" = "diff" ]; then
  helm template \
      --namespace external-dns-gateway-api --no-hooks --skip-tests external-dns-gateway-api \
      $SCRIPT_DIR \
      --values $SCRIPT_DIR/values.yaml | kubectl diff --namespace external-dns-gateway-api -f -
  exit 0
fi
