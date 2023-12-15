# eCamp v3 API

After starting the project using `docker compose up -d`, you can visit the API documentation in the browser at http://localhost:3000/api

To use the API, you will need to log in. You can use the "Login" endpoint offered in the Swagger UI for this. The example credentials should work fine in development.

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
docker-compsoe stop php; docker compose rm php; docker compose up
```  
if you don't want to restart the frontend.
