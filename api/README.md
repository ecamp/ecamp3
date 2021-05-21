# eCamp v3 API

After starting the project using `docker-compose up -d`, you can visit the API documentation under https://localhost:4001/docs

The php container will automatically output an authentication token that can be used in the API documentation to log in. You can see the instructions using `docker-compose logs php`

In order to get another token, you can use one of the following commands:
```
# Either use curl to make a real API request that will give you the token:
curl -k -X POST -H "Content-Type: application/json" https://localhost/authentication_token -d '{"username":"test-user","password":"test"}'

# Or use the command from the container's instructions:
echo "Bearer $(docker-compose exec php bin/console lexik:jwt:generate-token test-user --no-debug | awk NF)"
```

If you use the API manually, the JWT token (starting with `ey...`) should be sent in a bearer authorization header:
```
Authorization: Bearer {token}
```
