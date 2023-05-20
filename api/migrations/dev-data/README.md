# dev Seed Data

Here are the migrations to add seed data for the local development,
the dev.ecamp3.ch deployment and for PR deployments.

## Modify the seed data

To change the seed data, you need to create a new "migration" so that
the changes are automatically available on the dev deployment and
for the other developers after the change is merged.
If you don't add a new "migration", the data on the other developer's machines and
on the dev deployment has to be manually deleted and the migrations have to be applied again.

1. Make sure that your development setup is running.

    ```shell
    docker compose up -d
    ```

2. Make sure that no unwanted changes are in the database.   
This deletes the data in your local development environment and ensures they only contain the seed data.

    ```shell
    docker compose stop php \
    && docker compose stop database \
    && docker compose rm -f database \
    && docker volume rm $(basename $(pwd))_db-data-postgres \
    && docker compose up -d
    ```

3. Perform the changes you want to make using the frontend
4. Run the dump script

    ```shell
    ./create-data-migration-dump.sh
    ```

5. If you want to change some things directly in the [data.sql](data.sql) script, you can do that now.
6. Stage all changes in this directory and commit them.
