# Performance tests

Run tests

```shell
docker compose run --rm performance-test run --quiet script.js | jq --sort-keys > output.json
```

Run tests

```shell
API_ROOT_URL="http://localhost:3000"

env_file=.env

if [ -f "$env_file" ]; then
  API_ROOT_URL=$(grep '^API_ROOT_URL=' "$env_file" | cut -d '=' -f 2 | tr -d '\r')
fi

datetime=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
filename=$(printf "%s" "$API_ROOT_URL" | sed 's|https?://||; s|/|_|g')_$datetime.json

docker compose run --rm performance-test run --quiet script.js | jq --sort-keys > measurements/$filename
```

Run tests once to debug

```shell
docker compose run --rm -e VUS=1 -e ITERATIONS=1 performance-test run script.js
```
