# Contributing :tada:
Thank you for wanting to help out! :heart:

Danke dass du mithelfen möchtest!
Die deutsche Version dieses Dokuments findest du [hier](./CONTRIBUTING_DE.md).

# [![Discord Join Banner](https://discordapp.com/api/guilds/1165624811800768702/widget.png?style=banner3)](https://discord.gg/tdwtRytV6P)

## [Code of conduct](https://www.ecamp3.ch/en/code-of-conduct) :page_with_curl:

## Workflow :gear:
This is a basic overview of the workflow, i.e. how we work with the code of eCamp v3.
More information about how to set up a development environment on your computer is in the [wiki](https://github.com/ecamp/ecamp3/wiki/installation).
If something about the setup is unclear, or you run into an error, there is a `setup-help` channel on [Discord](https://discord.gg/tdwtRytV6P) for you to ask questions and ask for help. :computer:
### Labels :label:
Issues are marked with labels and some of them are not self-explanatory and are explained here:
- **Good first issue**: :green_heart: Beginner friendly issues.
- **Type-Labels**: Tells you what part of the architecture is involved. There are `type: Frontend`, `type: Print`, `type: Deployment` & `type: API` the architecture for those are partially documented in the [wiki](https://github.com/ecamp/ecamp3/wiki/architecture-frontend)
- **Needs prototype**: :bulb: If you have an idea how to solve this issue: we'd like to see it. This issue needs a prototype before actual implementation begins since the specifications are somewhat vague. A prototype can be many things, whether your prototype is a sketch, mockup, partial implementation or something else is up to you.
- **Feature request**: :rocket: An idea/request for a functionality, not ready to be implemented but ready to be discussed.

### :point_right: Starting with an issue
To get started, find an issue that interests you. If you are new, we recommend selecting a [Good first issue](https://github.com/ecamp/ecamp3/labels/Good%20first%20issue).
Please note that for other issues we recommend ones with the label [Ready for Implementation](https://github.com/ecamp/ecamp3/issues?q=is%3Aopen+is%3Aissue+label%3A%22Ready+for+implementation%22), which signifies that the issue should have clear definitions. If you have any questions, feel free to ask.
If you are working on an issue, please leave a comment so that we can assign it to you, to make sure that the specifications are still up-to-date, and to prevent two people working on the same issue.
Alternatively, open a draft pull request and mention the issue ID to signal that you are working on that particular issue.
Please note that while the wiki can be helpful in understanding the project, it's not exhaustive (meaning there might be parts missing or out of date).
If you have any questions, please comment on the issue for clarification or ask on Discord. We are happy to help and answer any questions you might have.

### Git setup :octocat:

We use a triangular git workflow. This means that all changes are first pushed to a contributor's fork of the repository, and then the changes are merged into the main fork via a pull request.
If you are an advanced git user you can set this up yourself.
In practice, setting up this workflow looks as follows:

1. Fork the main repository onto your GitHub account. To use the commands your configured git `user.name` should be exactly your git user name.
   If you run the code below, and it outputs your GitHub username you are good to go.
    ```shell
    echo $(git config user.name)
    ```
   If not you should replace the `$(git config user.name)` parts with your username or run `git config --global user.name "YourUsername"` with your GitHub username instead of `YourUsername`


2. Clone the main repository to your local computer:

   ```shell
   git clone https://github.com/ecamp/ecamp3.git
   cd ecamp3
   ```

3. Add your fork as a remote:

   ```shell
   git remote add "$(git config user.name)" "https://github.com/$(git config user.name)/ecamp3.git"   
   ```

4. Configure the central repo for pulling the current state and your own repo for pushing new changes:

   ```shell
   git config remote.pushdefault "$(git config user.name)"
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


### Before submitting pull requests :incoming_envelope:

#### Code formatting :art:

We use cs-fixer for PHP and ESLint and Prettier for Javascript to ensure a common code style. Make sure your code is auto-formatted before committing and pushing to the repository.
We recommend to [configure your IDE](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting) such that your code is auto-formatted on save.

Alternatively you can

- <details>
    <summary>run php-cs-fixer and ESLint / Prettier manually before each commit: (Click me, I am expandable) </summary>

    ```shell
    # Frontend fixes in running container
    docker compose exec frontend npm run lint
    
    # API/PHP fixes in running container
    docker compose exec php composer cs-fix
    
    # Print fixes in running container
    docker compose exec print npm run lint
    
    # PDF fixes in running container
    docker compose exec pdf npm run lint
    
    # E2E fixes are always run like this
    docker compose run --rm --entrypoint="npm run lint" e2e
    ```
  If you don't have a container of that type running use `run` instead of `execute`. Note that this will start a new Docker container (which might not be desired on a device with limited computing resources).
    ```shell
    docker compose run --rm frontend npm run lint
    docker compose run --rm php composer cs-fix
    docker compose run --rm print npm run lint
    docker compose run --rm pdf npm run lint
    ```
  </details>
- set up a pre-commit [Git-Hook](https://www.atlassian.com/git/tutorials/git-hooks) to run php-cs-fixer and ESLint automatically before each commit, you can find an example in the [pre-commit.sh](./pre-commit.sh) file.
<details>
  <summary>To use this example as a Git-Hook run the following commands (Click me, I am expandable)</summary>
    <strong>Consider examining the file before running random code from a public Git repo.</strong>

```shell
# Ensure the file is executable
chmod +x .git/hooks/pre-commit
# Create a link, alternatively use 'cp' instead of 'ln' to copy
ln ./pre-commit.sh .git/hooks/pre-commit
# Lets see how long execution takes
time .git/hooks/pre-commit
```
</details>

#### Checklist :pencil:

We truly value and appreciate every contribution to our project! :heart:
To make the collaboration smoother and more enjoyable for everyone,
we've put together this checklist :scroll:.
Following it will not only enhance the quality and consistency of your contributions :sparkles: but also fast-track the review process. :rocket:


- [x] **Sync with Central Repository:** :arrows_counterclockwise: Ensure your fork is up to date with the central repository, facilitating a smooth merge. [GitHub Docs](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork)
- [x] **Lint:** :wrench: Ensure that linters have run over all new or modified files
- [x] **Significant Changes:** :mag_right: Confirm that every modified file contributes meaningful content, steering clear of inconsequential changes like mere whitespace adjustments.
- [x] **Testing:** :test_tube: Write tests for any new features, and update existing ones if you've made changes to functionalities.
- [x] **Language & Spelling:** :book: Use English for all variable names, class names, functions, comments, etc., and ensure that all added content has been spellchecked.
- [x] **Sensitive Information:** :no_entry: Before submitting, double-check to ensure no passwords, credentials, or local configurations are present in your changes.
- [x] **Continuous Integration:** :green_circle: Confirm that the GitHub Actions CI build finishes successfully without test failures.

## Database :floppy_disk:

### Using Dev-Data for Local Development :construction_worker:
For ease of development and to ensure consistency across local environments,
we provide a predefined dataset known as 'Dev-Data'.
This dataset is tailored to streamline the testing process and ensure that features,
including edge cases, are effectively covered.

### Recommended Test User :bust_in_silhouette:
To login on dev environments like [localhost:3000](http://localhost:3000) utilize the `test@example.com / test` user credentials.
This user has been populated with a comprehensive set of camps that should suffice for testing most features and scenarios.

### Feedback on Dev-Data :loudspeaker:
We constantly strive to improve our 'Dev-Data'.
If you identify gaps or believe there's an additional scenario it should cover,
please open an issue to let us know.

### Documentation :mag:
For a deeper understanding of 'Dev-Data', you can refer to its dedicated [README](./api/migrations/dev-data/README.md).

### Consistent Testing Across Environments :globe_with_meridians:
'Dev-Data' is replicated across all development environments.
We encourage its use for consistent testing.
When reporting an issue or bug, consider referencing a specific example from 'Dev-Data'.
Since the data, including IDs, remains consistent, it allows everyone to easily replicate and understand the behavior you're highlighting.

## Discord :speech_balloon:
We understand that setting up a development environment can sometimes be tricky,
especially with varying systems and configurations.
If you encounter any issues or face roadblocks during the setup,
please don't hesitate to join our Discord server.
Our core team and community are happy to help you there.
In fact, we encourage you to ask questions, collaborate, and seek support on Discord whenever you hit a roadblock on an Issue.
Your feedback not only helps us refine the setup process for everyone but also ensures that potential issues are addressed promptly.
Remember, our support isn't limited to just setup concerns; you're welcome to discuss any other topics you encounter in our Discord community.

[![Discord Join Banner](https://discordapp.com/api/guilds/1165624811800768702/widget.png?style=banner4)](https://discord.gg/tdwtRytV6P)