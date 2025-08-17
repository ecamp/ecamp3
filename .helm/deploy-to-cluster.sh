#!/bin/sh

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

ACTION="${1:-diff}"

export NAME=$(jq -r '.NAME' $SCRIPT_DIR/ecamp3/env.yaml)

"$REPO_DIR/.helm/ecamp3/deploy.sh" "${ACTION}"
