#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")

helm upgrade --install ecamp3-logging --namespace=ecamp3-logging --create-namespace $SCRIPT_DIR
