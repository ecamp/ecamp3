# Contributing

> Für eine deutsche Übersetzung siehe unten.

eCamp is currently being redeveloped in this repository by a core developer team. This team consists of the original developers of eCamp v2, as well as a few other interested individuals. We are currently re-building the functionality of eCamp v2 in a more extensible and flexible way, before starting any new features.

## Feature requests
If you have some feature requests, please direct them to cosinus@gryfensee.ch, so we can build a list of requirements and wishes for future features. We are well aware of the need for offline usability and mobile friendly design. However, please note that it is still a long way until the program will be completed.

## Helping out with development
If you are a developer and wish to get involved in the project, please leave us a message at cosinus@gryfensee.ch. We will then get in touch with you once the project is ready for community development. Until then, feel free to set up the project on your local machine and make yourself familiar with it. Setup documentation can (soon) be found in our Wiki pages on GitHub.

## Contribution process
We use a triangular git workflow. This means that all changes are first pushed to a contributor's fork of the repository, and then the changes are merged into the main fork via a pull request. In practice, setting up this workflow looks as follows:

1. Fork the main repository onto your GitHub account

2. Clone your fork to your local computer:

    ```
    git clone https://github.com/your-username/ecamp3.git
    cd ecamp3
    ```

3. Add the master repository as a remote and name the remotes in a sensible way:

    ```
    git remote add ecamp3 https://github.com/ecamp3/ecamp3.git
    git remote rename origin mine
    ```

4. Configure the central repo for pulling the current state and your own repo for pushing new changes:

    ```
    git config remote.pushdefault mine
    git config push.default current
    ```

Once this is set up, you can start coding, and all `git pull` commands should pull from the central repository by default, while all `git push` commands will push to your fork of the project.

We use cs-fixer to ensure a common code style. To make cs-fixer run before every commit, create a script <your>/<local>/<repository>/ecamp3/.git/hooks/pre-commit with the following content and make it executable (for Windows, use equivalent commands):
```
#!/usr/bin/env bash

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER="vendor/bin/php-cs-fixer"
HAS_PHP_CS_FIXER=false

if [ -x vendor/bin/php-cs-fixer ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        $PHP_CS_FIXER fix --config-file=.php_cs --verbose "$line";
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require --dev fabpot/php-cs-fixer:dev-master"
    echo ""
fi

echo "php-cs-fixer pre commit hook finish"
```

# Mitarbeit (deutsche Übersetzung)

eCamp wird momentan in diesem repository von einem kleinen Entwicklerteam neu entwickelt. Dieses Team besteht aus den ursprünglichen Entwicklern von eCamp v2, sowie einzelnen weiteren interessierten Personen. Aktuell bauen wir die Funktionalität von eCamp v2 auf eine erweiterbare und flexible Art neu, erst später werden neue Funktionen hinzugefügt.

## Wünsche und Anregungen
Falls du Wünsche und Anregungen für neue Funktionen hast, sende diese bitte an cosinus@gryfensee.ch. So können wir eine Wunschliste für zukünftige Versionen aufbauen. Uns ist bewusst, dass eCamp in Zukunft offlinefähig und Handy-freundlich gestaltet werden muss. Es ist aber noch ein weiter Weg bis dahin.

## Bei der Entwicklung mithelfen
Wenn du ein Programmierer bist und beim Projekt mitwirken möchtest, melde dich bitte bei cosinus@gryfensee.ch. So können wir dich kontaktieren, sobald das Projekt bereit ist für die Entwicklung in einer Community. Bis dahin kannst du das Projekt bei dir lokal aufsetzen und dich mit der Architektur vertraut machen. Dokumentation dazu findest du (bald) auf unseren Wiki-Seiten hier auf GitHub.

## Mitarbeits-Prozess
Wir wenden einen triangulären Git-Workflow an. Das bedeutet, dass alle Code-Änderungen zuerst auf dem Fork des Entwicklers veröffentlicht werden, und erst dann werden die Änderungen via Pull Request in das zentrale Repository eingefügt. Um dies einzurichten, befolgen wir folgende Schritte:

1. Erstelle einen persönlichen Fork des zentralen Repositories auf GitHub

2. Klone den Fork auf deinen lokalen Computer:

    ```
    git clone https://github.com/dein-username/ecamp3.git
    cd ecamp3
    ```

3. Füge das zentrale Repository als Remote hinzu und benenne deinen Fork sinnvoll:

    ```
    git remote add ecamp3 https://github.com/ecamp3/ecamp3.git
    git remote rename origin mine
    ```

4. Konfiguriere Git, sodass es als aktuellen, offiziellen Code-Stand das zentrale Repository und fürs Veröffentlichen neuer Änderungen den Fork verwendet:

    ```
    git config remote.pushdefault mine
    git config push.default current
    ```

Wenn dies eingerichtet ist kannst du loslegen, und alle `git pull`-Befehle sollten standardmässig den Code vom zentralen Repository holen und `git push`-Befehle sollten auf deinen eigenen Fork des Projekts senden.

Wir verwenden cs-fixer um einen gemeinsamen Code Style zu etablieren. Um cs-fixer automatisch vor jedem Commit auszuführen, erstelle ein neues Skript <your>/<local>/<repository>/ecamp3/.git/hooks/pre-commit mit dem folgenden Inhalt (muss für Windows ev. leicht angepasst werden):
```
#!/usr/bin/env bash

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER="vendor/bin/php-cs-fixer"
HAS_PHP_CS_FIXER=false

if [ -x vendor/bin/php-cs-fixer ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        $PHP_CS_FIXER fix --config-file=.php_cs --verbose "$line";
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require --dev fabpot/php-cs-fixer:dev-master"
    echo ""
fi

echo "php-cs-fixer pre commit hook finish"
```
