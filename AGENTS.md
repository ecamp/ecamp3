# AGENTS.md

The default branch of this project is `devel` branch of the `ecamp/ecamp3` repository. Base new work on it.

## Dev environment tips

Before you do anything, start the dev environment:

```bash
docker compose up -d
docker compose --profile=playwright-cli up -d
```

### Key Directories

- `/api/` Symfony/API Platform backend.
- `/frontend/` Main Vue 3 frontend served by Vite.
- `/frontend-old/` Legacy Vue 2 frontend; NEVER CHANGE THIS.
- `/common/` Shared JavaScript utilities, ESLint rules, and locale files used by the Node-based apps.
- `/frontend/src/pdf/` Client-side PDF rendering code used by the frontend.
- `/print/` Separate, Nuxt-based print backend.
- `/e2e/` Playwright end-to-end tests.
- `/.helm/` Helm chart and Kubernetes deployment configuration.
- `/.ops/` Operational tooling such as performance tests.

## General development instructions

Think extra hard when developing. Do not just use the easiest way to achieve a goal.
Try to achieve the goal with minimal changes.
If you hit a problem, read the docs for this problem to find the correct solution.
Always ask before you remove assertions or expectations in tests.
Explain your changes in the commit message.
Make small, reviewable commits.

Always run commands in the existing Docker services with `docker compose exec <service> ...`.
Use the package manager scripts from the affected directory instead of invoking tools directly.

For documentation, YAML, and JSON changes at the repository root, use the root npm scripts:

```bash
docker compose run --rm prettier npm run lint:check
docker compose run --rm prettier npm run lint
```

### Code comments and documentation

Comments in code mostly age poorly. Please use the following framework to decide how to document changes:

1. Make the code speak for itself: split and name methods, variables and tests so they are self-explanatory.
   Behaviour covered by a test needs no prose.
2. Prefer commit messages to code comments, because a git blame is more telling and easier to research than
   figuring out the repo code state back when a comment was written. Match the style and length of other,
   human-made commits. No figures of speech, just objective facts in short form. NEVER more than 1 commit
   line + 3 lines of commit message.
3. Only if the information is directly about the class / method being commented, only if it can't be expressed
   through the above means of documentation adequately, and only if it is relevant every single time a piece of
   code is read, forever: Only then create a comment. Even then, only include the information that isn't
   covered in the other docs locations, and don't mention the other docs locations either. If a decision is
   detailed in an issue, it can be referenced in the commit message, so no need for rephrasing or linking.
4. API docblocks on entity properties are still wanted, they feed the OpenAPI documentation.

**Guiding Principles** for writing documentation and especially the rare comments:

- NEVER reference comments, method names or implementation locations from other files. These will
  be outdated very quickly.
- NEVER narrate, retell or even mention earlier versions or decisions from previous iterations in
  the conversation history, or how it used to be implemented. Any documentation must be written
  as if this was the first ever implementation, and understandable by anyone without any history,
  context or memories.
- When there are other comments in the touched files already, any new comments MUST be shorter
  and rarer than the existing ones. MAXIMUM BREVITY.

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
When changing shared code, run checks for every affected consumer, commonly `frontend`, or `print`.
When changing shared locale files, also consider the `/translation` instructions.

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

## Playwright CLI service

The `playwright-cli` Docker service provides Playwright browser automation.
It uses `network_mode: host` so URLs like `http://localhost:3000` work the same as on the host.
The container stays running so the playwright-cli session daemon persists across commands.

```bash
docker compose exec playwright-cli playwright-cli open http://localhost:3000
docker compose exec playwright-cli playwright-cli snapshot
docker compose exec playwright-cli playwright-cli close
```

For one-shot scripts, use `exec` against the running container:

```bash
docker compose exec playwright-cli node /workspace/.playwright-cli/my-script.js
```

Screenshots and snapshots are saved to `.playwright-cli/`.
Find the skill here: [skills](.agents/skills).

## Infrastructure and operations

- `docker-compose.yml` and `docker-compose.override.yml` define the local development services.
- `reverse-proxy-nginx.conf` configures the local reverse proxy.
- `/.helm/` contains deployment manifests and environment examples; validate changes with Helm tooling when available.
- `/.ops/performance-test/` contains performance test tooling; keep generated artifacts out of commits unless explicitly requested.

## PR instructions

- Ensure tests are green
- Ensure code is formatted
- Ensure code is linted
