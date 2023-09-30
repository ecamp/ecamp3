# db_backup_job

This folder contains the files used in the db_backup_job.\
It also contains a docker-compose.yml file to easily test the [create-backup.sh](create-backup.sh) locally.

1. Copy the [.env-example](.env-example) file to [.env](.env)

    ```shell
    cp .env-example .env
    ```

2. Fill in the variables of [.env](.env)

3. Run the image with the script

    ```shell
    docker compose run --rm create-backup
    ```
