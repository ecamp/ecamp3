set -e

tmpfile=$(mktemp)

cat << 'EOF' | tee -a $tmpfile
BEGIN;

WITH profile as (SELECT id from profile WHERE email = 'support@ecamp3.ch'),
     update_password as (UPDATE "user" SET password = '$2y$13$KTCSklVQHNvbwJQ3Awl8Ee7t0wJB1gfRBXDANeQlBblqwJ4wgOEmC' WHERE profileid = (SELECT id FROM profile)),
     update_email as (UPDATE "profile" SET email = 'test@example.com' WHERE id = (SELECT id FROM profile))
SELECT 1;

COMMIT;

EOF

psql $DATABASE_URL -v ON_ERROR_STOP=1 < $tmpfile
