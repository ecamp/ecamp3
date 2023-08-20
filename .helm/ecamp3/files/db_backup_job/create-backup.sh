#!/bin/sh
set -e
set -x

touch /tmp/backup-dir/test

pg_dump $DATABASE_URL \
      --no-owner \
      --no-privileges \
      --column-inserts \
      --rows-per-insert 10000 \
      > /tmp/backup-dir/pgdump.sql
gzip /tmp/backup-dir/pgdump.sql
if [ -n "${ENCRYPTION_KEY}" ]; then
    echo "Encrypting backup"
    gpg --passphrase=${ENCRYPTION_KEY} --batch -c /tmp/backup-dir/pgdump.sql.gz
fi
touch /tmp/backup-dir/.backup-complete
ls -la /tmp/backup-dir
