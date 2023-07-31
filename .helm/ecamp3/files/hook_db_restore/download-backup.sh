#!/bin/sh
set -e

working_dir=/tmp/backup-dir

DEST_FILE=$working_dir/pgdump.sql.gz
if [ -n "${ENCRYPTION_KEY}" ]; then
    DEST_FILE="${DEST_FILE}.gpg"
fi

export AWS_ACCESS_KEY_ID=$S3_ACCESS_KEY_ID
export AWS_SECRET_ACCESS_KEY=$S3_ACCESS_KEY

if [ $SOURCE_FILE = "latest" ]; then
  prefix=$S3_BUCKET/$APP_NAME/
  SOURCE_FILE=$(aws s3api list-objects \
                          --endpoint-url $S3_ENDPOINT \
                          --bucket $S3_BUCKET \
                          --prefix "$prefix" \
                          --query 'sort_by(Contents, &LastModified)[-1].Key' \
                          --output text)
fi

first_version_id=$(aws s3api list-object-versions \
                          --endpoint-url $S3_ENDPOINT \
                          --bucket $S3_BUCKET \
                          --prefix $SOURCE_FILE \
                          --query 'sort_by(Versions, &LastModified)[0].VersionId' \
                          --output text)

echo "Downloading version $first_version_id of $SOURCE_FILE from $S3_BUCKET"

aws s3api get-object \
           --endpoint-url $S3_ENDPOINT \
           --bucket $S3_BUCKET \
           --key $SOURCE_FILE \
           --version-id $first_version_id \
           $DEST_FILE

touch $working_dir/.download-complete
