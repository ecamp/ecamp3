# hook_db_restore

This folder contains the files used in the hook_db_restore.\
It also contains a docker-compose.yml file to easily test the [restore-backup.sh](restore-backup.sh) locally.

1. Copy the [.env-example](.env-example) file to [.env](.env)

    ```shell
    cp .env-example .env
    ```

2. Fill in the variables of [.env](.env)

3. Run the image with the script

    ```shell
    docker compose run --rm restore-backup
    ```
4. Maybe locally: change username and password of the support account
   That we don't need sharp credentials in the local performance testing.

    ```shell
    docker compose run --rm restore-backup sh update-support-email.sh
    ```

