#!/bin/sh
set -e
set -x

backup_dir=/var/backup-dir
touch /tmp/backup-dir/test
touch $backup_dir/test

pg_dump $DATABASE_URL \
      --no-owner \
      --no-privileges \
      --column-inserts \
      --rows-per-insert 10000 \
      > $backup_dir/pgdump.sql
gzip $backup_dir/pgdump.sql
if [ -n "${ENCRYPTION_KEY}" ]; then
    echo "Encrypting backup"
    gpg --passphrase=${ENCRYPTION_KEY} --batch -c /tmp/backup-dir/pgdump.sql.gz
fi
touch $backup_dir/.backup-complete
ls -la $backup_dir
