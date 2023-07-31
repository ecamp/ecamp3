# ecampV3 AWS Setup

We currently use AWS S3 for additional DB Backups.
Because AWS is a little complicated, we use the aws-cli and [Pulumi](https://www.pulumi.com) to provision
the setup for aws.

## Initial setup

1. Make a personal user with the ecampCore Amazon User of the KeePass
2. Login with your personal user here: <https://d-90679ce1c6.awsapps.com/start#/>
3. Configure [.aws/credentials](.aws/credentials) according to the according to option 2 of
   "Command line or programmatic access".\
   Name the profile "default"

## Working with the cdk

1. Make sure the dependencies are up to date

   ```shell
   docker compose run --rm aws-setup npm ci
   ```

2. Fix the permissions of the node_modules folder

   ```shell
   sudo chown -R $USER:$USER .
   ```

3. Set the aws bucket as state backend

   ```shell
   docker compose run --rm aws-setup pulumi login s3://ecampcore-pulumi/
   ```

4. Choose the stack

   ```shell
   docker compose run --rm aws-setup pulumi stack select
   ```

5. Deploy your changes

   ```shell
   docker compose run --rm aws-setup pulumi up
   ```

6. Get secrets

   ```shell
   docker compose run --rm aws-setup pulumi stack output --show-secrets
   ```

7. Lint

   ```shell
   docker compose run --rm aws-setup npm run lint
   ```
