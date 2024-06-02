#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")

KIBANA_HOST=${KIBANA_HOST:-localhost:5601}

curl -X POST $KIBANA_HOST/api/saved_objects/_export \
      -H 'kbn-xsrf: true' \
      -H 'Content-Type: application/json' \
      -d '
      {
        "type": [
          "dashboard", 
          "index-pattern",
          "search"
        ],
        "excludeExportDetails": true
      }' \
      --silent \
      | jq -S \
      > $SCRIPT_DIR/kibana-objects.ndjson
