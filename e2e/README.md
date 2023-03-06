# End-to-end tests

As a pre-requisite for running end-to-end tests, we assume you have the application fully up and running on your system.
If not, please follow the documentation links in the README.md in the root of this repository.

## Option A: Run end-to-end tests in Docker container (headless)
```
docker compose --profile e2e run --rm e2e
```

### Run tests using a specific browser
```
# Supported values: chrome, edge, electron (default), firefox
docker compose --profile e2e run --rm e2e --browser chrome
```

### Open the cypress UI and visually see the tests run
```
# Only necessary on Mac OS: install xhost installieren. Restart your computer after this.
brew cask install xquartz

# Allow the container to open a window on the host (only required once per computer restart)
xhost local:root

# Open the cypress UI
docker compose --profile e2e run --entrypoint "cypress open --project ." e2e
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
