# End-to-end tests

As a pre-requisite for running end-to-end tests, we assume you have the application fully up and running on your system.
If not, please follow the documentation links in the README.md in the root of this repository.

## Option A: Run end-to-end tests in Docker container (headless)

### Preparation
```shell
# Only necessary on Mac OS: install xhost. Restart your Mac after this.
brew cask install xquartz

# Only necessary on Mac OS and Linux, and only once per computer restart:
# Allow the Cypress Docker container to open a window on the host
xhost local:root
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
docker compose --profile e2e run --entrypoint "cypress open --project ." e2e
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
