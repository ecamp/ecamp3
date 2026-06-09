# AGENTS.md

## Dev environment tips

Before you do anything, start the dev environment:

```bash
docker compose up -d
```

### Key Directories

- `/api/` Symfony/API Platform backend.
- `/frontend/` Main Vue 3 frontend served by Vite.
- `/frontend-old/` Legacy Vue 2 frontend; NEVER CHANGE THIS.
- `/common/` Shared JavaScript utilities, ESLint rules, and locale files used by the Node-based apps.
- `/pdf/` Client-side PDF rendering package used by the frontends.
- `/print/` Nuxt-based print backend.
- `/e2e/` Playwright end-to-end tests.
- `/.helm/` Helm chart and Kubernetes deployment configuration.
- `/.ops/` Operational tooling such as performance tests.

## General development instructions

Think extra hard when developing. Do not just use the easiest way to achieve a goal.
Try to achieve the goal with minimal changes.
If you hit a problem, read the docs for this problem to find the correct solution.
Always ask before you remove assertions or expectations in tests.
Explain your changes in the commit message.

Always run commands in the existing Docker services with `docker compose exec <service> ...`.
Use the package manager scripts from the affected directory instead of invoking tools directly.

For documentation, YAML, and JSON changes at the repository root, use the root npm scripts:

```bash
docker compose run --rm prettier npm run lint:check
docker compose run --rm prettier npm run lint
```

## /api directory

### Development instructions

Use composer to run scripts, don't use phpunit directly.

### Testing instructions

> **Note**: PHP test take very long to run, never run them all at once.
> Only run specific tests or tests for one entity when needed

```bash
docker compose exec api composer test <path to test from api directory>

# always run cs fix before a commit
docker compose exec api composer cs-fix

# lint
docker compose exec api composer psalm
docker compose exec api composer phpstan
```

## /frontend directory

Main Vue 3 frontend. The Docker service name is `frontend`, and commands run from `/app` inside the container.

```bash
docker compose exec frontend npm run lint
docker compose exec frontend npm run test:unit
docker compose exec frontend npm run build
```

For targeted frontend changes, prefer focused unit tests or lint checks before running broader commands.

## /frontend-old directory

Legacy Vue 2 frontend. The Docker service name is `frontend-old`.
NEVER CHANGE THIS

## /common directory

Shared code and locale files are mounted into the Node-based services.
When changing shared code, run checks for every affected consumer, commonly `frontend`, `pdf` or `print`.
When changing shared locale files, also consider the `/translation` instructions.

## /pdf directory

Client-side PDF rendering package. The Docker service name is `pdf`.

```bash
docker compose exec pdf npm run lint:check
docker compose exec pdf npm run lint
docker compose exec pdf npm run test:unit
docker compose exec pdf npm run build
```

## /print directory

Nuxt-based print backend. The Docker service name is `print`.

```bash
docker compose exec print npm run lint:check
docker compose exec print npm run lint
docker compose exec print npm run test
docker compose exec print npm run build
```

## /e2e directory

Playwright end-to-end tests. The service uses the `e2e` Docker profile, so start it when needed.
See [README.md](e2e/README.md)

## Infrastructure and operations

- `docker-compose.yml` and `docker-compose.override.yml` define the local development services.
- `reverse-proxy-nginx.conf` configures the local reverse proxy.
- `/.helm/` contains deployment manifests and environment examples; validate changes with Helm tooling when available.
- `/.ops/performance-test/` contains performance test tooling; keep generated artifacts out of commits unless explicitly requested.

## PR instructions

- Ensure tests are green
- Ensure code is formatted
- Ensure code is linted
