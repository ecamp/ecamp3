# Deployment mit Docker

Hier wird erklärt, wie eCamp auf einem Webserver deployt werden kann, auf dem Docker installiert ist. Eine solche Instanz läuft auf DigitalOcean unter https://dev.ecamp3.ch.

Wenn du eCamp einfach nur nutzen willst, dann empfehlen wir dir dringend die bereits installierte Version auf [https://ecamp.pfadiluzern.ch](https://ecamp.pfadiluzern.ch) zu verwenden.
Wenn du an eCamp mitentwickeln willst, kannst du der Anleitung für die lokale Installation auf deinem Computer folgen ([mit Docker auf Linux](./install-docker.md) / [mit Docker auf Windows + WSL 2](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows)).

## Übersicht
Auf [Docker Hub](https://hub.docker.com/u/ecamp) sind jeweils offizielle Docker images für Backend, Frontend und weitere Komponenten der aktuellsten Version von eCamp veröffentlicht.
Diese kann man herunterladen, die Konfiguration via [Bind Mounts](https://docs.docker.com/storage/bind-mounts/) in den Container schreiben, und die Container deployen.
Ausserdem benötigt eCamp v3 eine Datenbank (wir verwenden aktuell MySQL 8) und für Print-Jobs und weiteres ein RabbitMQ (wir verwenden aktuell v3.8).

Diese Installation kann zudem automatisiert werden, sodass jedes mal automatisch deployed wird, wenn etwas an eCamp geändert wird (natürlich solange die Unit Tests und end-to-end Tests noch laufen).

## Konfiguration der Docker images von Docker Hub
Mindestens die folgenden Konfigurations-Dateien sollten für ein Deployment überschrieben werden:

##### Backend
* `/app/config/autoload/doctrine.local.prod.php` (Datenbank-Konfiguration von [DoctrineModule](https://github.com/doctrine/DoctrineModule/blob/master/docs/authentication.md))
* `/app/config/autoload/mail.local.prod.php` (E-Mail-Konfiguration von [laminas-mail](https://docs.laminas.dev/laminas-mail/transport/intro/))
* `/app/config/autoload/zfr_cors.global.php` (CORS-Konfiguration von [zfr-cors](https://github.com/zf-fr/zfr-cors#configuring-the-module), Frontend-URL und Print-Preview-URL eintragen)
* `/app/config/autoload/amq.local.prod.php` (Konfiguration der Verbindung zu RabbitMQ mit [amqp_bunny](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/transport/amqp_bunny.md#create-context))
* `/app/config/autoload/sessions.local.php` (die Domain zu der das Session-Cookie gehören soll, und weitere Einstellungen von [laminas-session](https://docs.laminas.dev/laminas-session/config/))
* Optional: `/usr/local/lib/php.ini` (PHP-Einstellungen)
* Optional: `/app/config/sentry.config.php` (Sentry DSN für Error Reporting)

##### Frontend
* `/app/environment.js` (URLs von Backend, print preview server, PDF file server, etc. und gegebenenfalls weitere exponierte Einstellungen)

##### Print preview
* Die Environment-Variablen in `print/print.env` sollten angepasst und in den Container hineingegeben werden

##### Puppeteer print worker
* `/app/environment.js` (URL von print preview server, RabbitMQ Zugang, Session Cookie Domain)

##### Weasy print worker
* `/app/environment.py` (URL von print preview server, RabbitMQ Zugang)

Natürlich können auch weitere Konfigurationsdateien oder beliebige Dateien im Backend oder den Workers überschrieben werden. Das Frontend und die Print Preview sind hingegen im Image bereits für Produktion transpiliert, daher ist es dort praktisch unmöglich, einzelne Änderungen via Bind Mounts / Volumes zu machen. Solche weiteren Konfigurations-Optionen müssen daher zuerst ins `environment.js` bzw. `print.env` ausgelagert werden. Wenn du eine solche zusätzliche Konfigurations-Möglichkeit brauchst, eröffne ein Issue.

## Automatisches Deployment (continuous integration)

Es ist für jeden möglich, automatisch custom CI-Builds zu triggern wenn sich etwas im Haupt-Repo von eCamp ändert. Dafür muss ein Fork erstellt werden, der dann via GitHub Actions mit dem origin synchron gehalten wird.
Der deployment-Branch des Forks sollte also durch den automatischen Sync immer den aktuellsten Stand des origin devels enthalten, plus ein paar wenige Commits (je weniger desto einfacher) die beliebige CI-Konfiguration enthalten. Die GitHub Actions Konfiguration muss im Haupt-Branch des Repositories liegen (bei uns `devel`).

Ein Beispiel für die GitHub Actions Konfiguration findest du unter https://github.com/ecamp/ecamp3/tree/devel/.github/workflows

Ein Beispiel für die CI-Konfiguration findest du unter https://github.com/ecamp/ecamp3/tree/deploy-dev.ecamp3.ch/.deployment und https://github.com/ecamp/ecamp3/tree/deploy-dev.ecamp3.ch/.travis.yml

### Warum nicht entweder Travis CI oder GitHub Actions? Warum beides?
GitHub Actions kann sehr viel einfacher als Travis CI das Git-Repository abändern (rebasen und pushen). Wollte man dasselbe auf Travis machen, so müsste man ein service account token erstellen, mit dem Travis das Git-Repository verändern kann.

Travis CI hat hingegen unter anderem den Vorteil gegenüber GitHub Actions, dass branch-spezifische Build Secrets möglich sind. Bei GitHub Actions sind alle Secrets allen Branches zugänglich. Besonders wenn man vom gleichen Fork aus an mehrere Orte deployen möchte (z.B. Testumgebung und Produktionsumgebung) ist das ein Nachteil. Du kannst natürlich für dich auch GitHub Actions fürs CI verwenden, dabei können wir dich aber nicht unterstützen.
