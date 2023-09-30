#!/bin/sh
set -e

echo "Creating backup"
current_date=$(date +"%Y-%m-%d-%H-%M-%S")
backup_dir=/var/backup-dir
src_file=$backup_dir/pgdump.sql.gz
dest_file="${current_date}-$APP_NAME.sql.gz"
mkdir -p $backup_dir

pg_dump $DATABASE_URL \
      --no-owner \
      --no-privileges \
      --column-inserts \
      --rows-per-insert 10000 \
      > $backup_dir/pgdump.sql
gzip $backup_dir/pgdump.sql

if [ -n "${ENCRYPTION_KEY}" ]; then
    echo "Encrypting backup"
    gpg --passphrase=${ENCRYPTION_KEY} --batch -c "$src_file"
    src_file="${src_file}.gpg"
    dest_file="${dest_file}.gpg"
fi

ls -la $backup_dir

echo "Uploading dump to $S3_BUCKET"

export AWS_ACCESS_KEY_ID=$S3_ACCESS_KEY_ID
export AWS_SECRET_ACCESS_KEY=$S3_ACCESS_KEY

set +e
aws --endpoint-url $S3_ENDPOINT s3 cp $src_file s3://$S3_BUCKET/$APP_NAME/$dest_file
exit_code=$?
rm -rf $backup_dir/*
rm -rf $backup_dir/.backup-complete
if [ $exit_code = 0 ]; then
    echo "uploaded dump successfully"
else
    echo "upload failed"
    exit $exit_code
fi
