# Contributing
Thank you for wanting to help out!

Danke dass du mithelfen möchtest! 
Die deutsche Version dieses Dokuments findest du [hier](./CONTRIBUTING_DE.md).

# English

## [Code of conduct](https://www.ecamp3.ch/en/code-of-conduct)

## Workflow
This is a basic overview of the workflow, i.e. how we work with the code of eCamp v3. More information about how to set up a development environment on your computer is in the [wiki](https://github.com/ecamp/ecamp3/wiki/installation).
If something about the setup is unclear, or you run into an error, there are [discussions](https://github.com/ecamp/ecamp3/discussions) on GitHub for you to ask questions and ask for help.
### Labels
Issues are marked with labels and some of them are not self-explanatory and are explained here:
- **Type-Labels**:
  Type labels follow the `type: *` format with the options `Frontend`, `Print`, `Deployment` & `API` the architecture for those are partially documented in the [wiki](https://github.com/ecamp/ecamp3/wiki/architecture-frontend)
- **Needs prototype**: If you have an idea how to solve this issue: we'd like to see it. This issue needs a prototype before actual implementation begins, since the specifications are somewhat vague. A prototype can be many things, whether your prototype is a sketch, mockup, partial implementation or something else is up to you.
- **Good first issue**: Beginner friendly issues.
- **Feature request**: An idea/request for a functionality, not ready to be implemented but ready to be discussed.

### Starting with an issue
To get started, find an issue that interests you. If you are new, we recommend selecting a [Good first issue](https://github.com/ecamp/ecamp3/labels/Good%20first%20issue).
Please note that for other issues we recommend ones with the label [Ready for Implementation](https://github.com/ecamp/ecamp3/issues?q=is%3Aopen+is%3Aissue+label%3A%22Ready+for+implementation%22), which signifies that the issue should have clear definitions. If you have any questions, feel free to ask.
If you are working on an issue, please leave a comment so that we can assign it to you, to make sure that the specifications are still up to date, and to prevent two people working on the same issue.
Alternatively, open a draft pull request and mention the issue ID to signal that you are working on that particular issue.
Please note that while the wiki can be helpful in understanding the project, it's not exhaustive. If you have any questions, please comment on the issue for clarification.

### Git setup

We use a triangular git workflow. This means that all changes are first pushed to a contributor's fork of the repository, and then the changes are merged into the main fork via a pull request. In practice, setting up this workflow looks as follows:

1. Fork the main repository onto your GitHub account. Let's say your GitHub account is called `your-username`.

2. Clone the main repository to your local computer:

   ```shell script
   git clone https://github.com/ecamp/ecamp3.git
   cd ecamp3
   ```

3. Add your fork as a remote:

   ```shell
   git remote add your-username https://github.com/your-username/ecamp3.git
   ```

4. Configure the central repo for pulling the current state and your own repo for pushing new changes:

   ```shell
   git config remote.pushdefault your-username
   git config push.default current
   ```

Once this is set up, you can start coding, and all `git pull` commands should pull from the central repository by default, while all `git push` commands will push to your fork of the project.

#### Checkout a feature branch

Before starting a new feature, you should do the following steps to start with a clean state that is easily mergeable later:

```shell
git fetch --all
git checkout origin/devel
git checkout -b my-new-feature-branch
```

### Code formatting

We use cs-fixer for PHP and ESLint and Prettier for Javascript to ensure a common code style. Make sure your code is auto-formatted before comitting and pushing to the repository.

We recommend to [configure your IDE](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting) such that your code is auto-formatted on save.

Alternatively you can

- run php-cs-fixer and ESLint / Prettier manually before each commit:
  ```shell
  docker compose run api composer cs-fix
  docker compose run frontend npm run lint
  docker compose run print npm run lint
  ```
- set-up a git pre-commit hook to run php-cs-fixer and ESLint automatically before each commit

### Before submitting pull requests

```shell
# Frontend fixes with docker
docker compose exec frontend npm run lint

# API/PHP fixes with docker
docker compose exec php composer run-script cs-fix

# Print fixes with docker
docker compose exec print npm run lint
```

- [x] Did cs-fixer run on all changed or new PHP files?
- [x] Did ESLint / Prettier run on all changed or new JS / Vue files?
- [x] Are all variables, classes, functions, comments etc. named or written in English?
- [x] Did you write tests for any new functionality or adapt the existing tests for changed functionality?
- [x] Are all passwords, credentials and local configuration removed from the code changes?
- [x] Do all changed files contain some important changes (as opposed to e.g. only whitespace changes)?
- [x] Is the fork up-to-date with the central repository and can your changes be merged automatically?
- [x] Did the GitHub Actions CI build run through without test failures?

## Database

### Using Dev-Data for Local Development
For ease of development and to ensure consistency across local environments, 
we provide a predefined dataset known as 'Dev-Data'. 
This dataset is tailored to streamline the testing process and ensure that features, 
including edge cases, are effectively covered.

### Recommended Test User
To begin with, utilize the `test@example.com / test` user credentials. 
This user has been populated with a comprehensive set of camps that should suffice for testing most features and scenarios.

### Feedback on Dev-Data
We constantly strive to improve our 'Dev-Data'. 
If you identify gaps or believe there's an additional scenario it should cover, 
please open an issue to let us know.

### Documentation
For a deeper understanding of 'Dev-Data', you can refer to its dedicated [README](./api/migrations/dev-data/README.md).

### Consistent Testing Across Environments
'Dev-Data' is replicated across all development environments. 
We encourage its use for consistent testing. 
When reporting an issue or bug, consider referencing a specific example from 'Dev-Data'. 
Since the data, including IDs, remains consistent, it allows everyone to easily replicate and understand the behavior you're highlighting.

## Discussions
We understand that setting up a development environment can sometimes be tricky, 
especially with varying systems and configurations. 
If you encounter any issues or roadblocks during the setup process, 
please don't hesitate to open a Discussion on GitHub. 
Our core team is more than happy to assist you. In fact, 
we encourage it! 
Your feedback not only helps us improve the setup process for everyone, 
but it also ensures that any potential issues are addressed promptly. 
And remember, 
it's not just limited to setup concerns—feel free to initiate discussions about anything else you might come across. 
We're here to help and collaborate!
