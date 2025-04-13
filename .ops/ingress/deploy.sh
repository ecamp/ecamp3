#!/bin/sh

set -ea

SCRIPT_DIR=$(realpath "$(dirname "$0")")
cd $SCRIPT_DIR

helm dep build

if [ $1 = "deploy" ]; then
  # to debug: --dry-run --debug
  helm upgrade --install ecamp3-ingress --namespace=ingress-nginx --create-namespace $SCRIPT_DIR
  exit 0
fi

if [ $1 = "diff" ]; then
  helm template \
      --namespace ingress-nginx --no-hooks --skip-tests ecamp3-ingress  \
      $SCRIPT_DIR  | kubectl diff --namespace ingress-nginx -f -
  exit 0
fi
