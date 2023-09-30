# Mitarbeit
Danke dass du mithelfen möchtest! :heart:

Thank you for wanting to help out!
The english version of this Document is available [here](./CONTRIBUTING.md).

# [![Discord Join Banner](https://discordapp.com/api/guilds/1165624811800768702/widget.png?style=banner3)](https://discord.gg/tdwtRytV6P)

## [Verhaltenskodex](https://www.ecamp3.ch/de/verhaltenskodex) :page_with_curl:

## Arbeitsablauf :gear:
Dies ist ein grundlegender Überblick über den Arbeitsablauf, d.h., wie wir mit dem Code von eCamp v3 arbeiten.
Weitere Informationen zur Einrichtung einer Entwicklungsumgebung auf deinem Computer findest du im [wiki](https://github.com/ecamp/ecamp3/wiki/installation).
Wenn etwas bei der Einrichtung unklar ist oder du auf einen Fehler gestossen bist, gibt es einen setup-help-Kanal auf [Discord](https://discord.gg/tdwtRytV6P), dort kannst du deine Fragen zum Setup stellen :computer:
### Labels :label:
Aufgaben werden mit Labels gekennzeichnet und einige davon sind nicht selbsterklärend und werden hier erklärt:
- **Good first issue**: :green_heart: Anfängerfreundliche Aufgaben.
- **Type-Labels**: Zeigen an, welcher Teil der Architektur betroffen ist. Es gibt `type: Frontend`, `type: Print`, `type: Deployment` & `type: API`. Die Architektur dafür ist teilweise im [Wiki](https://github.com/ecamp/ecamp3/wiki/architecture-frontend) dokumentiert.
- **Needs prototype**: :bulb: Wenn du eine Idee hast, wie dieses Problem gelöst werden kann, würden wir sie gerne sehen. Dieses Problem benötigt einen Prototypen, bevor die eigentliche Implementierung beginnt, da die Spezifikationen etwas vage sind. Ein Prototyp kann vieles sein: Ob Prototyp, Skizze, Mock-up, eine Teil-Implementierung oder etwas anderes ist, liegt bei dir.
- **Feature request**: :rocket: Eine Idee/Anfrage für eine Funktionalität, die noch nicht zur Implementierung, aber zur Diskussion bereitsteht.

### :point_right: Mit einem Issue (Ticket) starten
Auf GitHub heissen die Arbeitspakete, welche du vielleicht als Tickets kennst, `Issues`. Um zu starten, suche dir ein Issue aus, das dich interessiert. Wenn du neu bist, empfehlen wir, ein [Good first issue](https://github.com/ecamp/ecamp3/labels/Good%20first%20issue) auszuwählen.
Falls dir diese zu einfach sind, empfehlen wir ein Issue mit dem Label [Ready for Implementation](https://github.com/ecamp/ecamp3/issues?q=is%3Aopen+is%3Aissue+label%3A%22Ready+for+implementation%22), diese sind klar spezifiziert. Wenn du Fragen hast, zögere nicht zu fragen.
Wenn du an einem Problem arbeitest, hinterlasse bitte einen Kommentar, damit wir es dir zuweisen können, um sicherzustellen, dass die Spezifikationen noch aktuell sind, und um zu verhindern, dass zwei Personen am selben Problem arbeiten.
Alternativ kannst du einen Entwurf für einen Pull-Request öffnen und die Issue-ID erwähnen, um zu signalisieren, dass du an diesem speziellen Problem arbeitest.
Bitte beachte, dass das Wiki zwar hilfreich sein kann, um das Projekt zu verstehen, aber es ist nicht vollständig (was bedeutet, dass Teile fehlen oder veraltet sein könnten).
Wenn du Fragen hast, hinterlasse ein Kommentar auf dem Issue oder melde dich über Discord. Wir helfen gerne und beantworten deine Fragen.

### Git einrichten:octocat:

Wir wenden einen triangulären Git-Workflow an. Das bedeutet, dass alle Code-Änderungen zuerst auf dem Fork des Entwicklers veröffentlicht werden, und erst dann werden die Änderungen via Pull Request in das offizielle eCamp3-Repository eingefügt.
Wenn du ein fortgeschrittener Git-Benutzer bist, kannst du dies selbst einrichten.
In der Praxis sieht die Einrichtung dieses Arbeitsablaufs wie folgt aus:

1. Erstelle einen persönlichen Fork des zentralen Repositories auf GitHub. Um die Befehle zu verwenden, sollte Ihr konfigurierter Git-Benutzername genau Ihrem GitHub-Benutzernamen entsprechen.
   Wenn du den folgenden Code ausführst und er dein GitHub-Benutzernamen ausgibt, bist du startklar.
    ```shell
    echo $(git config user.name)
    ```
   Wenn nicht, solltest du den `$(git config user.name)` durch deinen Benutzernamen ersetzen oder den Befehl `git config --global user.name "DeinNutzername"` mit deinem GitHub-Benutzernamen anstelle von `DeinNutzername` ausführen.


3. Klone das originale Repository auf deinen lokalen Computer:

   ```shell
   git clone https://github.com/ecamp/ecamp3.git
   cd ecamp3
   ```

3. Füge deinen Fork als Remote hinzu:

   ```shell
   git remote add "$(git config user.name)" "https://github.com/$(git config user.name)/ecamp3.git"
   ```

4. Konfiguriere Git, sodass es als aktuellen, offiziellen Code-Stand das zentrale Repository und fürs Veröffentlichen neuer Änderungen den Fork verwendet:

   ```shell
   git config remote.pushdefault "$(git config user.name)"
   git config push.default current
   ```

Wenn dies eingerichtet ist kannst du loslegen, und alle `git pull`-Befehle sollten standardmässig den Code vom zentralen Repository holen und `git push`-Befehle sollten auf deinen eigenen Fork des Projekts senden.

#### Ein neuen Feature-Branch auschecken

Bevor du etwas am Code änderst, solltest du jeweils die folgenden Schritte durchführen, um mit einem sauberen Stand zu starten der später einfach gemerged werden kann:

```bash
git fetch --all
git checkout origin/devel
git checkout -b my-new-feature-branch
```


### Bevor du Pull Requests einreichst:incoming_envelope:

#### Codeformatierung :art:

Wir verwenden cs-fixer für PHP und ESLint und Prettier für Javascript, um einen einheitlichen Codestil zu gewährleisten. Stelle sicher, dass dein Code automatisch formatiert wird, bevor du ihn committest und in das Repository stellst.
Wir empfehlen dir, deine Entwicklungsumgebung so zu [Konfigurieren](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting), dass der Code beim Speichern automatisch formatiert wird.

Alternativ kannst du

- <details>
    <summary>php-cs-fixer und ESLint / Prettier manuell vor jedem Commit ausführen: (Klick mich an, ich bin erweiterbar) </summary>

    ```shell
    # Frontend dateien in einem bereits laufenden Container formatieren
    docker compose exec frontend npm run lint
    
    # API/PHP dateien in einem bereits laufenden Container formatieren
    docker compose exec php composer cs-fix
    
    # Print dateien in einem bereits laufenden Container formatieren
    docker compose exec print npm run lint
    
    # PDF dateien in einem bereits laufenden Container formatieren
    docker compose exec pdf npm run lint
    
    # E2E dateien in einem bereits laufenden Container formatieren
    docker compose run --rm --entrypoint="npm run lint" e2e
    ```
  Wenn du keinen Container dieses Typs laufen hast, verwende `run` anstelle von `execute`. Beachte, dass dies einen neuen Docker-Container startet (was auf einem Gerät mit begrenzten Rechenressourcen eventuell nicht erwünscht ist).
    ```shell
    docker compose run --rm frontend npm run lint
    docker compose run --rm php composer cs-fix
    docker compose run --rm print npm run lint
    docker compose run --rm pdf npm run lint
    ```
  </details>
- einen pre-commit [Git-Hook](https://www.atlassian.com/git/tutorials/git-hooks) aufsetzen, um php-cs-fixer und ESLint automatisch vor jedem Commit ausführen zu lassen. Ein Beispiel dafür ist in der [pre-commit.sh](./pre-commit.sh) datei zu finden.
<details>
  <summary>Um dieses Beispiel zu verwenden führe die folgenden Befehle aus (Klick mich an, ich bin erweiterbar)</summary>
    <strong>Beachte, du gibst nun einer Datei aus dem Internet die Berechtigung auf deinem Computer ausgeführt zu werden. Es ist Empfehlenswert in solchen Fällen sicherzustellen das du dem Code und Author vertraust. (Oder noch besser: verstehst, was der Code macht)</strong>

```shell
# Datei ausführbar machen
chmod +x .git/hooks/pre-commit
# Einen Verweiss auf die Datei erstellen, alternativ kannst du 'cp' anstelle von 'ln' verwenden um die Datei zu kopieren
ln ./pre-commit.sh .git/hooks/pre-commit
# Sieh dir an wie Lange die ausführung dauert und stelle sicher, dass alles funktioniert
time .git/hooks/pre-commit
```
</details>

#### Prüfliste :pencil:

Wir schätzen jeden Beitrag zu unserem Projekt sehr und sind dankbar dafür! :heart:
Um die Zusammenarbeit für alle reibungsloser und angenehmer zu gestalten,
haben wir diese Checkliste zusammengestellt :scroll:.
Durch das Prüfen dieser Punkte verbesserst du die Qualität und Konsistenz deiner Beiträge :sparkles: und beschleunigt den Überprüfungsprozess :rocket:.


- [x] **Synchronisation mit dem zentralen Repository:** :arrows_counterclockwise: Stelle sicher, dass dein Fork auf dem neuesten Stand des zentralen Repositories ist, um eine reibungslose Zusammenführung zu ermöglichen. [GitHub-Dokumentation](https://docs.github.com/de/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork)
- [x] **Lint:** :wrench: Stelle sicher, dass Linters auf alle neuen oder geänderten Dateien angewendet wurden.
- [x] **Bedeutsame Änderungen:** :mag_right: Bestätige, dass jede geänderte Datei einen sinnvollen Inhalt beiträgt und vermeide unbedeutende Änderungen wie reine Anpassungen von Leerzeichen.
- [x] **Testing:** :test_tube: Schreibe Tests für alle neuen Funktionen und aktualisiere bestehende Tests, wenn du Änderungen an Funktionalitäten vorgenommen hast.
- [x] **Sprache & Rechtschreibung:** :book: Verwende Englisch für alle Variablennamen, Klassennamen, Funktionen, Kommentare usw., und stelle sicher, dass aller hinzugefügter Inhalt Rechtschreibprüfungen durchlaufen hat.
- [x] **Sensible Informationen:** :no_entry: Überprüfe vor dem Einreichen noch einmal, um sicherzustellen, dass keine Passwörter, Zugangsdaten oder lokale Konfigurationen in deinen Änderungen vorhanden sind.
- [x] **Kontinuierliche Integration:** :green_circle: Stelle sicher, dass der GitHub Actions CI-Build erfolgreich abgeschlossen werden kann, ohne Testfehler.

## Datenbank :floppy_disk:

### Verwende Dev-Data für die Lokale Entwicklung :construction_worker:
Um die Entwicklung zu vereinfachen und Konsistenz in lokalen Umgebungen zu gewährleisten,
bieten wir einen vordefinierten Datensatz an, der als 'Dev-Daten' bekannt ist.
Dieser Datensatz ist darauf ausgerichtet, den Testprozess zu optimieren und eine diverse
Datenabbildung zu erlangen um auch Randfälle zu Testen.

### Empfohlener Testbenutzer :bust_in_silhouette:
Um dich in Entwicklungsumgebungen wie [localhost:3000](http://localhost:3000) einzuloggen, verwende die Benutzerdaten `test@example.com / test`.
Dieser Benutzer wurde mit einer umfassenden Sammlung von Lagern ausgestattet, die für das Testen der meisten Funktionen und Szenarien ausreichen sollten.

### Rückmeldungen zu den Testdaten :loudspeaker:
Wir bemühen uns ständig, unsere 'Dev-Daten' zu verbessern.
Wenn du Lücken erkennst oder glaubst, dass es ein zusätzliches Szenario abdecken sollte,
öffne bitte ein Issue, um uns darüber zu informieren.

### Dokumentation  :mag:
Für ein tieferes Verständnis der 'Dev-Daten' haben wir dieses [README](./api/migrations/dev-data/README.md) (nur auf Englisch verfügbar) erstellt.

### Konsistentes Testen in verschiedenen Umgebungen :globe_with_meridians:
Die 'Dev-Daten' werden in allen Entwicklungsumgebungen repliziert.
Wir empfehlen deren Verwendung für konsistente Tests.
Wenn du ein Problem oder einen Fehler meldest: Referenziere doch ein spezifisches Beispiel aus den 'Dev-Daten'.
Da die Daten, einschließlich der IDs, konstant bleiben, kann jeder das von dir hervorgehobene Verhalten leicht nachvollziehen und reproduzieren.

## Discord :speech_balloon:
Wir verstehen, dass das Einrichten einer Entwicklungsumgebung manchmal knifflig sein kann,
besonders bei unterschiedlichen Systemen und Konfigurationen.
Wenn du auf Probleme stösst oder Hindernisse während der Einrichtung begegnest,
zögere bitte nicht, unserem Discord-Server beizutreten.
Unser Kernteam und die Community helfen dir dort gerne weiter.
Tatsächlich ermutigen wir dich, Fragen zu stellen, zu kollaborieren und Unterstützung auf Discord zu suchen, wann immer du bei einem Problem auf einen Stolperstein triffst.
Dein Feedback hilft nicht nur dabei, den Einrichtungsprozess für alle zu verfeinern, sondern stellt auch sicher, dass potenzielle Probleme umgehend angegangen werden.
Denk daran, unsere Unterstützung beschränkt sich nicht nur auf Einrichtungsfragen; du bist herzlich eingeladen, in unserer Discord-Community über alle anderen Themen zu diskutieren, auf die du in deiner Umsetzung stösst.

[![Discord Join Banner](https://discordapp.com/api/guilds/1165624811800768702/widget.png?style=banner4)](https://discord.gg/tdwtRytV6P)