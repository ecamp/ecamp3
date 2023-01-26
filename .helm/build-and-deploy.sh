#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

. $"$SCRIPT_DIR"/build-images.sh
. $"$SCRIPT_DIR"/deploy-to-cluster.sh
