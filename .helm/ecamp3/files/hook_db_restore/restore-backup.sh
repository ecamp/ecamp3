#!/bin/sh
set -e

working_dir=/tmp/backup-dir
complete_marker=$working_dir/.download-complete

timeout 10s sh <<EOT
  while [ ! -f $complete_marker ]; do
      sleep 0.1
  done
EOT

backup_file=$working_dir/pgdump.sql

if [ -n "${ENCRYPTION_KEY}" ]; then
    echo "Decrypting backup"
    gpg --passphrase=${ENCRYPTION_KEY} --batch -d $backup_file.gz.gpg > $backup_file.gz;
fi
set +e
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

psql $DATABASE_URL < $sql_file_for_restore


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
rm -rf $complete_marker
