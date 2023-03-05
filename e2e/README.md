# end-to-end tests

## Option A: Run end-to-end tests in Docker container (headless)

```
docker compose up -d
docker run -v $PWD:/e2e -w /e2e --network host cypress/included:12.7.0
```

## Option B: Run end-to-end tests locally

### Install cypress

```
npm install
```

### Run end-to-end tests (CLI)

```
docker compose up -d
npm run cypress:run
```

### Open cypress test runner

```
docker compose up -d
npm run cypress:open
```
