# eCamp v3 API

After starting the project using `docker-compose up -d`, you can visit the API documentation under https://localhost:4001/docs

To use the API, you will need to log in. You can use the "Login" endpoint offered in the Swagger UI for this.

### Manually using the API without a browser
If you ever need to get an API token for manual use, you can use the following command:
```
# Or use the command from the container's instructions:
docker-compose exec php bin/console lexik:jwt:generate-token test-user --no-debug
```

The token must then be split and sent in two cookies to the API. The header and payload (from `ey` until before the second period `.`) must be sent in a cookie named `jwt_hp`. The signature (everything after the second period `.`) must be sent in a cookie called `jwt_s`.
See https://jwt.io for more info on the structure of JWT tokens, and https://medium.com/lightrail/getting-token-authentication-right-in-a-stateless-single-page-application-57d0c6474e3 for more info on why this split cookie approach is a good idea for SPAs.
