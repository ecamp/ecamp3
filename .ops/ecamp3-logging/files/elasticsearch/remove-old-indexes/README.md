# remove old indexes

With the 2G Memory we use in prod, the elasticsearch life cycle management
does not work properly.
Thus we have this small script that does that.

1. Copy the [.env-example](.env-example) file to [.env](.env)

   ```shell
   cp .env-example .env
   ```

2. Fill in the variables of [.env](.env)

3. Do a port forward of the elasticsearch port if necessary

   ```shell
   kubectl -n ecamp3-logging port-forward services/elasticsearch 9200:9200
   ```

4. Run the image with the script

   ```shell
   docker compose run --rm remove-old-indexes
   ```
