# eCamp v3 API

After starting the project using `docker compose up -d`, you can visit the API documentation in the browser at http://localhost:3000/api

To use the API, you will need to log in. You can use the "Login" endpoint offered in the Swagger UI for this. The example credentials should work fine in development.

### Image uploads

Camp members and managers can upload an image using `POST /camps/{campId}/images`
with a multipart form field named `file`. The JSON response contains `id`, `src`,
`mediaType`, `byteSize`, `width` and `height`. The `src` identifies the camp image.

`GET /camps/{campId}/images/{id}` checks camp read access before validating the
SHA-256 ETag. Images in public camps can be read anonymously. Responses use
`Cache-Control: private, no-cache`; matching conditional requests return 304.
Caddy serves the local file or proxies a short-lived signed S3 request. PHP does
not read the image bytes. The internal delivery headers are removed by Caddy,
and image requests bypass Varnish. The API must run behind the supplied Caddy
configuration; direct PHP responses contain internal storage information.

JPEG, PNG and WebP inputs are converted to WebP with EXIF orientation applied and
metadata removed. Images are never upscaled. Limits are configured through
`IMAGE_MAX_BYTES`, `IMAGE_MAX_PIXELS`, `IMAGE_MAX_DIMENSION` and `IMAGE_WEBP_QUALITY`.
PHP's `upload_max_filesize`, `post_max_size` and upstream request limits must also
permit the configured upload size. Available PHP memory can impose a lower limit.

`IMAGE_STORAGE=local` stores files in `IMAGE_STORAGE_PATH`, outside the public
directory. Deployments need persistent storage accessible to their API replicas.
For S3-compatible storage, set `IMAGE_STORAGE=s3`, `IMAGE_STORAGE_BUCKET`,
`IMAGE_STORAGE_REGION` and optionally `IMAGE_STORAGE_ENDPOINT` and
`IMAGE_STORAGE_PATH_STYLE`. Credentials use the AWS SDK credential chain, including
`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` and optionally `AWS_SESSION_TOKEN`.
The bucket must remain private.

`IMAGE_STORAGE_PREFIX` isolates deployments within a bucket or local directory.
Files use `images/{sha256}` below this prefix. The hash is calculated from the
converted bytes. Repeated uploads reuse the global blob and its camp association.
Changing storage configuration does not migrate existing files. Storage writes
cannot be rolled back with database transactions; failed transactions can leave
unreferenced files without a database record; these are not covered by cleanup.

### Image cleanup

Run `app:cleanup-camp-images` followed by `app:cleanup-image-blobs` using `php bin/console`.
Each command marks unused records, clears the timestamp of referenced records and
deletes records whose unused period exceeds the configured retention.
`CAMP_IMAGE_RETENTION_DAYS` and `IMAGE_BLOB_RETENTION_DAYS` must be positive integers
and default to 7. The blob retention period starts when its last camp image is gone.
Blob cleanup also removes the stored file; storage failures retain the database
record for a subsequent attempt. Both commands report their changes.

The Helm CronJob is enabled with `IMAGE_CLEANUP_ENABLED=true`. Its defaults are
`IMAGE_CLEANUP_SCHEDULE="0 3 * * *"` and `IMAGE_CLEANUP_TIME_ZONE="Europe/Zurich"`.
It runs both commands sequentially. For local storage, `IMAGE_STORAGE_EXISTING_CLAIM`
must name a persistent volume claim accessible to the API and cleanup pods.
For S3, `IMAGE_STORAGE_CREDENTIALS_SECRET` can name a Secret containing AWS credential
environment variables; workload credentials from the AWS SDK chain also work.

### Manually using the API without a browser

If you ever need to get an API token for manual use, you can use the following command:

```shell
docker compose exec php bin/console lexik:jwt:generate-token test@example.com --no-debug
```

The token must then be split and sent in two cookies to the API. The header and payload (from `ey` until before the second period `.`) must be sent in a cookie named `[api-domain]_jwt_hp`. The signature (everything after the second period `.`) must be sent in a cookie called `[api-domain]_jwt_s` (replace [api-domain] with the domain where the API is served, e.g. `localhost_jwt_hp` or `pr1234.ecamp3.ch_jwt_s`).
See https://jwt.io for more info on the structure of JWT tokens, and https://medium.com/lightrail/getting-token-authentication-right-in-a-stateless-single-page-application-57d0c6474e3 for more info on why this split cookie approach is a good idea for SPAs.

### Code quality

We are using the following toolchain to ensure code quality standards:

- **PHP CS Fixer**\
  Run
  ```shell
  docker compose exec php composer cs-fix
  ```
  before committing\
  cs-check is integrated into CI (pull request will not pass)
- **Phpstan**\
  Run
  ```shell
  docker compose exec php composer phpstan
  ```
  phpstan is integrated into CI (pull request will not pass)
- **Psalm**\
  Run
  ```shell
  docker compose exec php composer psalm
  ```
  psalm is integrated into CI (pull request will not pass)

### Debugging

For debugging you need to add the env variables XDEBUG_MODE and XDEBUG_CONFIG for xdebug in $REPOSITORY_ROOT/.env\
`XDEBUG_MODE=debug,coverage`\
Additional possible value: trace\
For phpstorm:\
`XDEBUG_CONFIG="client_host=docker-host idekey=PHPSTORM log_level=0"`\
For vscode:\
`XDEBUG_CONFIG="client_host=docker-host idekey=VSCODE log_level=0"`

After you changed the .env file, you need to recreate the container that the change has an effect.

```shell
docker compose down && docker compose up
```

or

```shell
docker compose stop api; docker compose rm api; docker compose up
```

if you don't want to restart the frontend.
