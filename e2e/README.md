# End-to-end tests

As a pre-requisite for running end-to-end tests, we assume you have the application fully up and running on your system.
If not, please follow the documentation links in the README.md in the root of this repository.

## Option A: Run end-to-end tests in Docker container (headless)

### Preparation

```shell
# Only necessary on Mac OS: install xhost. Restart your Mac after this.
brew cask install xquartz
```

```shell
# Only necessary on Mac OS and Linux, and only once per computer restart:
# Allow the Cypress Docker container to open a window on the host
xhost local:root
```

### Install dependencies

```shell
docker compose --profile e2e run --rm --entrypoint "npm ci" e2e
```

### Update dependencies

```shell
docker compose --profile e2e run --rm --entrypoint "npm update" e2e
```

or

```shell
docker compose --profile e2e run --rm --entrypoint "npm update <dependency>" e2e
```

### Run all e2e tests

```shell
docker compose --profile e2e run --rm e2e --browser chrome
```

### Run a specific e2e test

```shell
docker compose --profile e2e run --rm e2e  --browser chrome --spec specs/login.cy.js
```

### Run tests using a specific browser

Supported browsers: `chrome`, `edge`, `electron` (default), `firefox`
Electron is currently not stable on the CI.

```shell
docker compose --profile e2e run --rm e2e --browser firefox
```

### Open the cypress UI and visually see the tests run

```shell
docker compose --profile e2e run --rm --entrypoint "cypress open --project ." e2e
```

## Option B: Run end-to-end tests locally

### Install cypress

```shell
npm install
```

### Run end-to-end tests (CLI)

```shell
docker compose up -d
npm run cypress:run
```

### Open cypress test runner

```shell
docker compose up -d
npm run cypress:open
```

### Run lint

```shell
docker compose run --rm --entrypoint="npm run lint" e2e
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
