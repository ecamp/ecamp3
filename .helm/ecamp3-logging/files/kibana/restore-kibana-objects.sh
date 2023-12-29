#!/bin/bash

SCRIPT_DIR=$(realpath "$(dirname "$0")")

KIBANA_HOST=${KIBANA_HOST:-localhost:5601}

tmp_file=/tmp/$(uuidgen).ndjson

cat $SCRIPT_DIR/kibana-objects.ndjson | jq -c > $tmp_file 

curl -X POST "$KIBANA_HOST/api/saved_objects/_import?createNewCopies=false&overwrite=true" \
     -H "kbn-xsrf: true" \
     --form file=@$tmp_file
