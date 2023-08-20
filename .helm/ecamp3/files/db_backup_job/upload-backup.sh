#!/bin/sh
set -e

timeout 300s sh <<EOT
  while [ ! -f /tmp/backup-dir/.backup-complete ]; do
      sleep 0.1
  done
EOT

echo "Uploading dump to $S3_BUCKET"
SRC_FILE=/tmp/backup-dir/pgdump.sql.gz

CURRENT_DATE=$(date +"%Y-%m-%d-%H-%M-%S")
DEST_FILE="${CURRENT_DATE}-$APP_NAME.sql.gz"

if [ -n "${ENCRYPTION_KEY}" ]; then
    SRC_FILE="${SRC_FILE}.gpg"
    DEST_FILE="${DEST_FILE}.gpg"
fi

export AWS_ACCESS_KEY_ID=$S3_ACCESS_KEY_ID
export AWS_SECRET_ACCESS_KEY=$S3_ACCESS_KEY

set +e
ls -lah /tmp/backup-dir/
aws --endpoint-url $S3_ENDPOINT s3 cp $SRC_FILE s3://$S3_BUCKET/$APP_NAME/$DEST_FILE
exit_code=$?
rm -rf /tmp/backup-dir/*
rm -rf /tmp/backup-dir/.backup-complete
if [ $exit_code = 0 ]; then
    echo "uploaded dump successfully"
else
    echo "upload failed"
    exit $exit_code
fi
