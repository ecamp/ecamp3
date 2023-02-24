<h1><span lang="en">Contributing</span> / <span lang="de">Mitarbeit</span></h1>

<span lang="en">Thank you for wanting to help out!</span> <em lang="de">Danke dass du mithelfen möchtest!</em>

<em lang="de">Die <a href="#deutsch">deutsche Übersetzung</a> findest du weiter unten.</em>

## English

### [Code of conduct](https://www.ecamp3.ch/en/code-of-conduct)

### Git setup

We use a triangular git workflow. This means that all changes are first pushed to a contributor's fork of the repository, and then the changes are merged into the main fork via a pull request. In practice, setting up this workflow looks as follows:

1. Fork the main repository onto your GitHub account. Let's say your GitHub account is called `your-username`.

2. Clone the main repository to your local computer:

   ```bash
   git clone https://github.com/ecamp/ecamp3.git
   cd ecamp3
   ```

3. Add your fork as a remote:

   ```bash
   git remote add your-username https://github.com/your-username/ecamp3.git
   ```

4. Configure the central repo for pulling the current state and your own repo for pushing new changes:

   ```bash
   git config remote.pushdefault your-username
   git config push.default current
   ```

Once this is set up, you can start coding, and all `git pull` commands should pull from the central repository by default, while all `git push` commands will push to your fork of the project.

### Starting a new feature

Before starting a new feature, you should do the following steps to start with a clean state that is easily mergeable later:

```bash
git fetch --all
git checkout origin/devel
git checkout -b my-new-feature-branch
```

### Code formatting

We use cs-fixer for PHP and ESLint for Javascript to ensure a common code style. Make sure your code is auto-formatted before comitting and pushing to the repository.

We recommend to [configure your IDE](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting) such that your code is auto-formatted on save.

Alternatively you can

- run php-cs-fixer and ESLint manually before each commit:
  ```bash
  docker compose run api composer cs-fix
  docker compose run frontend npm run lint
  docker compose run print npm run lint
  ```
- set-up a git pre-commit hook to run php-cs-fixer and ESLint automatically before each commit

### Before submitting pull requests

- [x] Did cs-fixer run on all changed or new PHP files?
- [x] Did ESLint run on all changed or new JS / Vue files?
- [x] Are all variables, classes, functions, comments etc. named or written in English?
- [x] Did you write tests for any new functionality or adapt the existing tests for changed functionality?
- [x] Are all passwords, credentials and local configuration removed from the code changes?
- [x] Do all changed files contain some important changes (as opposed to e.g. only whitespace changes)?
- [x] Is the fork up-to-date with the central repository and can your changes be merged automatically?
- [x] Did the GitHub Actions CI build run through without test failures?

## Deutsch

### [Verhaltenskodex](https://www.ecamp3.ch/de/verhaltenskodex)

### Git einrichten

Wir wenden einen triangulären Git-Workflow an. Das bedeutet, dass alle Code-Änderungen zuerst auf dem Fork des Entwicklers veröffentlicht werden, und erst dann werden die Änderungen via Pull Request in das offizielle eCamp3-Repository eingefügt. Um dies einzurichten, befolgen wir folgende Schritte:

1. Erstelle einen persönlichen Fork des zentralen Repositories auf GitHub. Nehmen wir an dein GitHub-Account heisst `your-username`.

2. Klone das originale Repository auf deinen lokalen Computer:

   ```bash
   git clone https://github.com/ecamp/ecamp3.git
   cd ecamp3
   ```

3. Füge deinen Fork als Remote hinzu:

   ```bash
   git remote add your-username https://github.com/your-username/ecamp3.git
   ```

4. Konfiguriere Git, sodass es als aktuellen, offiziellen Code-Stand das zentrale Repository und fürs Veröffentlichen neuer Änderungen den Fork verwendet:

   ```bash
   git config remote.pushdefault your-username
   git config push.default current
   ```

Wenn dies eingerichtet ist kannst du loslegen, und alle `git pull`-Befehle sollten standardmässig den Code vom zentralen Repository holen und `git push`-Befehle sollten auf deinen eigenen Fork des Projekts senden.

### Ein neues Feature beginnen

Bevor du etwas am Code änderst, solltest du jeweils die folgenden Schritte durchführen, um mit einem sauberen Stand zu starten der später einfach gemerged werden kann:

```bash
git fetch --all
git checkout origin/devel
git checkout -b my-new-feature-branch
```

### Quellcode Formatierung

Wir verwenden php-cs-fixer für PHP und ESLint für JS um einen gemeinsamen Code Style zu etablieren. Bitte stelle sicher, dass dein Code automatisch richtig formatiert wird, bevor du diesen ins Git repo committest.

Wir empfehlen deine [IDE so zu konfigurieren](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting), dass dein Code beim Speichern automatisch richtig formatiert wird.

Alternativ kannst du

- php-cs-fixer und ESLint vor jedem commit manuell laufen lassen:
  ```bash
  docker compose run api composer cs-fix
  docker compose run frontend npm run lint
  docker compose run print npm run lint
  ```
- einen git pre-commit hook einrichten, der php-cs-fixer und ESLint vor jedem commit triggert

### Vor dem Einreichen eines Pull Requests

- [x] Wurden alle geänderten oder neuen PHP-Dateien von cs-fixer verarbeitet?
- [x] Wurden alle geänderten oder neuen JS/Vue-Dateien von ESLint bereinigt?
- [x] Sind alle Variabeln, Klassen, Funktionen, Kommentare, etc. auf englisch benannt?
- [x] Hast du für neue Funktionalität Tests geschrieben und für geänderte Funktionalität die Tests angepasst?
- [x] Wurden alle Passwörter, Zugangsdaten und lokale Konfiguration aus den Code-Änderungen entfernt?
- [x] Enthalten alle geänderten Dateien auch wirklich eine sinnvolle Änderung (im Gegensatz zu z.B. nur Whitespace-Änderungen)?
- [x] Ist der Fork auf dem aktuellen Stand des zentralen Repositories und können die Änderungen automatisch gemerged werden?
- [x] Ist der GitHub Actions CI-Build erfolgreich und ohne Test Failures durchgelaufen?
