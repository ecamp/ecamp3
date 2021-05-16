# eCamp v3 API

After starting the project using `docker-compose up`, you can visit the API documentation under https://localhost/docs

If you already have a user: To log in to the application, use the following command to get a JWT token:
```
curl -k -X POST -H "Content-Type: application/json" https://localhost/authentication_token -d '{"email":"johndoe@test.com","password":"test"}'
```

The JWT token can then be entered in the "Authorize" menu of the API documentation, or if you use the API manually, the token should be sent in a bearer authorization header:
```
Authorization: Bearer {token}
```
