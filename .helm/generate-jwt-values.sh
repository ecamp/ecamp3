#!/bin/bash
set -euo pipefail

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

TMP_DIR=$(mktemp -d)

jwt_passphrase=$(uuidgen)
echo -n "$jwt_passphrase" | openssl genpkey -out "$TMP_DIR"/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
echo -n "$jwt_passphrase" | openssl pkey -in "$TMP_DIR"/private.pem -passin stdin -out "$TMP_DIR"/public.pem -pubout

jwt_public_key=$(cat "$TMP_DIR"/public.pem)
jwt_private_key=$(cat "$TMP_DIR"/private.pem)

tmp="$TMP_DIR/tmp.yaml"

json_file="$SCRIPT_DIR/ecamp3/env.yaml"

if [ ! -f $json_file ]; then
  cp "$SCRIPT_DIR/ecamp3/env.example.yaml" $json_file
fi

jq --arg v "$jwt_passphrase" '.JWT_PASSPHRASE = $v' < $json_file > "$tmp"
mv "$tmp" $json_file

jq --arg v "$jwt_public_key" '.JWT_PUBLIC_KEY = $v' < $json_file > "$tmp"
mv "$tmp" $json_file

jq --arg v "$jwt_private_key" '.JWT_PRIVATE_KEY = $v' < $json_file > "$tmp"
mv "$tmp" $json_file

rm -rf "$TMP_DIR"
