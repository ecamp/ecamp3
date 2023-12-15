#!/bin/bash
set -e

working_dir=/var/backup-dir
mkdir -p $working_dir

backup_file=$working_dir/pgdump.sql
DEST_FILE=$backup_file.gz
if [ -n "${ENCRYPTION_KEY}" ]; then
    DEST_FILE="${DEST_FILE}.gpg"
fi

export AWS_ACCESS_KEY_ID=$S3_ACCESS_KEY_ID
export AWS_SECRET_ACCESS_KEY=$S3_ACCESS_KEY
export AWS_PAGER=

if [ $SOURCE_FILE = "latest" ]; then
  prefix="$APP_NAME"
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

if [ -n "${ENCRYPTION_KEY}" ]; then
    echo "Decrypting backup"
    gpg --passphrase=${ENCRYPTION_KEY} --batch -d $DEST_FILE > $backup_file.gz;
fi

gunzip $backup_file.gz

ls -lah $working_dir

sql_file_for_restore=/tmp/restore.sql
cat << 'EOF' | tee -a $sql_file_for_restore
BEGIN;

DO $$ DECLARE
    r RECORD;
BEGIN
    FOR r IN (
        SELECT tablename 
        FROM pg_tables 
        WHERE 
            schemaname = 'public'
        )
    LOOP
        EXECUTE 'DROP TABLE ' || quote_ident(r.tablename) || ' CASCADE';
    END LOOP;
END $$;

EOF

cat $backup_file >> $sql_file_for_restore

cat << 'EOF' | tee -a $sql_file_for_restore
COMMIT;
EOF

psql $DATABASE_URL -v ON_ERROR_STOP=1 < $sql_file_for_restore


if [ "$INVITE_SUPPORT_ACCOUNT_TO_INTERESTING_CAMPS" = "true" ]; then
  cat << 'EOF' | psql $DATABASE_URL
  CREATE EXTENSION IF NOT EXISTS pgcrypto;
  
  INSERT INTO camp_collaboration (id, status, role, createtime,
                                  updatetime, userid, campid)
      (WITH interesting_camps as ((SELECT c.id
                                   FROM camp c
                                            JOIN activity a on c.id = a.campid
                                   GROUP BY c.id, c.title
                                   ORDER BY count(a.id) DESC
                                   LIMIT 10)
                                  UNION
                                  (SELECT id
                                   FROM ((SELECT c.id
                                          FROM camp c
                                                   JOIN activity a on c.id = a.campid
                                          GROUP BY c.id, c.title
                                          ORDER BY count(a.id) DESC)
                                         INTERSECT
                                         (SELECT c.id
                                          FROM camp c
                                                   JOIN period p on c.id = p.campid
                                          WHERE p."end" >= now())) as a
                                   LIMIT 10))
       SELECT encode(gen_random_bytes(6), 'hex'),
              'established',
              'manager',
              now(),
              now(),
              u.id,
              ic.id
       FROM interesting_camps ic,
            profile p
                JOIN "user" u ON p.id = u.profileid
       WHERE p.email IN (
           'support@ecamp3.ch'
           )
         AND ic.id NOT IN (SELECT campid FROM camp_collaboration WHERE userid = u.id));
EOF
fi

rm -rf $working_dir/*
