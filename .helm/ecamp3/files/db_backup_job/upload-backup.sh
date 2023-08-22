#!/bin/sh
set -e

backup_dir=/var/backup-dir

timeout 300s sh <<EOT
  while [ ! -f $backup_dir/.backup-complete ]; do
      ls -la $backup_dir
      sleep 1
  done
EOT
ls -la /tmp/backup-dir
set -x

echo "Uploading dump to $S3_BUCKET"
SRC_FILE=$backup_dir/pgdump.sql.gz

CURRENT_DATE=$(date +"%Y-%m-%d-%H-%M-%S")
DEST_FILE="${CURRENT_DATE}-$APP_NAME.sql.gz"

if [ -n "${ENCRYPTION_KEY}" ]; then
    SRC_FILE="${SRC_FILE}.gpg"
    DEST_FILE="${DEST_FILE}.gpg"
fi

export AWS_ACCESS_KEY_ID=$S3_ACCESS_KEY_ID
export AWS_SECRET_ACCESS_KEY=$S3_ACCESS_KEY

set +e
ls -lah $backup_dir
aws --endpoint-url $S3_ENDPOINT s3 cp $SRC_FILE s3://$S3_BUCKET/$APP_NAME/$DEST_FILE
exit_code=$?
rm -rf $backup_dir/*
rm -rf $backup_dir/.backup-complete
if [ $exit_code = 0 ]; then
    echo "uploaded dump successfully"
else
    echo "upload failed"
    exit $exit_code
fi
