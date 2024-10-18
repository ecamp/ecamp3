# Performance tests

Run tests

```shell
docker compose run --rm performance-test run --quiet script.js | jq --sort-keys > output.json
```

Run tests once to debug


```shell
docker compose run --rm -e VUS=1 -e ITERATIONS=1 performance-test run script.js
```
