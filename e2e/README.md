# End-to-end tests

As a pre-requisite for running end-to-end tests, we assume you have the application fully up and running on your system.
If not, please follow the documentation links in the README.md in the root of this repository.

## Structure

The e2e tests are structured into 3 groups of tests:

### [0-snapshot-tests](tests/0-snapshot-tests)

Tests to make sure that views look the same as before.
**They must not change state** which allows us to run the in parallel.
They are also run with multiple browsers and viewports to make sure we didn't change something accidentally.

### [5-cross-browser-tests](tests/5-cross-browser-tests)

Tests to make sure that something works in all browsers.
That can be a hack that we needed for a certain browser (this doesn't happen that often anymore, but it happens)
or very important behavior we want to be sure it works on all browsers.
We try to not modify state in this category as long as possible that we can keep them parallelized,
but they might need to change state at some point.

### [9-behavior-tests](tests/9-behavior-tests)

Tests that we don't have to run in multiple browsers and viewports because they mostly test the interaction between
the frontend and other services. They must not rely on a certain database state and cannot be run in parallel as
they might influence the state of other tests.

## Option A: Run end-to-end tests in Docker container (headless)

### Install dependencies

```shell
docker compose --profile e2e run --rm e2e npm ci
```

### Update dependencies

```shell
docker compose --profile e2e run --rm e2e npm update
```

or

```shell
docker compose --profile e2e run --rm e2e "npm update <dependency>"
```

### Optional preparation to simulate conditions closer to CI

This switches off HMR and starts the frontend using a production build, like on CI.
Please note that in this mode, when changing things in the frontend e.g. to fix a broken e2e test, you will have to run this command again every time (takes roughly 10 seconds).

```shell
CI=true docker compose up -d --force-recreate frontend
```

### Run all e2e tests

```shell
docker compose --profile e2e run --rm e2e npx playwright test
```

### Run a specific e2e test

```shell
docker compose --profile e2e run --rm e2e npx playwright test tests/5-cross-browser-tests/login.spec.ts
```

### Run tests using a specific browser

Supported browsers: `chromium`, `firefox`, `webkit`

```shell
docker compose --profile e2e run --rm e2e npx playwright test --project firefox
```

### Open Playwright UI mode in container

```shell
docker compose --profile e2e run --rm e2e npx playwright test --ui-host=localhost --ui-port=8080
```

Then open <http://localhost:8080> in your browser.

### Show test report

```shell
open playwright-report/index.html
```

### Show trace

```shell
docker compose --profile e2e run --rm e2e npx playwright show-trace <your-trace-zip-file> --host=localhost --port=8080
```

Then open <http://localhost:8080> in your browser.

### Cleanup the frontend to run with HMR again

You can skip this in case you didn't do the optional `CI=true` setup step above.

```shell
docker compose up -d --force-recreate frontend
```

### Update browser after branch switch

If a branch switch leads to a different playwright version, the local playwright version might be outdated.
To update the local playwright version, run npm ci:

See [Install dependencies](#install-dependencies)

## Option B: Run end-to-end tests locally

### Install dependencies

```shell
npm install
npx playwright install
```

### Run end-to-end tests (CLI)

```shell
docker compose up -d
npm test
```

### Open Playwright UI

```shell
docker compose up -d
npm run test:ui
```

### Run lint

```shell
docker compose --profile e2e run --rm e2e npm run lint
```

## For both options: run against prod api image

### Run the dev api image to generate jwt pair

```shell
docker compose up -d --wait
```

### Build the prod api image

```shell
docker compose -f ../docker-compose.yml build api
```

### Run the prod api image

```shell
docker compose -f ../docker-compose.yml up --wait -d api
```
