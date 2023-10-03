# db-backup-restore-image

This folder contains the image to create backups from the database and
upload them to aws, or to download backups and restore them.\
It also contains gpg to encrypt/decrypt the backups.\
The docker-compose.yml file makes it easy to build and push the image locally.\
(and later we can use the docker-compose.yml to build it in the github action too)

1. If you want to change the variables (e.g. container registry, docker hub user)

    ```shell
    export CONTAINER_REGISTRY=docker.io
    export REPO_OWNER=bacluc
    export VERSION=local
    ```

2. build the image

    ```shell
    docker compose build
    ```

3. push the image

    ```shell
    docker compose push
    ```
