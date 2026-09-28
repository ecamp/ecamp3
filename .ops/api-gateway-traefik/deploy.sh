#!/bin/sh

set -ea

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR

helm dep build

action=${1:-diff}
if [ "$action" = "deploy" ]; then
  kubectl apply --server-side --force-conflicts \
    --filename=https://github.com/kubernetes-sigs/gateway-api/releases/download/v1.6.1/standard-install.yaml
  # to debug: --dry-run --debug
  helm upgrade --install api-gateway-traefik --namespace=api-gateway --create-namespace $SCRIPT_DIR
  exit 0
fi

if [ "$action" = "diff" ]; then
  helm template \
      --namespace api-gateway --no-hooks --skip-tests api-gateway-traefik  \
      $SCRIPT_DIR  | kubectl diff --namespace api-gateway -f -
  exit 0
fi
