# Mitarbeit
Danke dass du mithelfen möchtest!

# Deutsch WIP

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

Wir verwenden php-cs-fixer für PHP und ESLint und Prettier für JS um einen gemeinsamen Code Style zu etablieren. Bitte stelle sicher, dass dein Code automatisch richtig formatiert wird, bevor du diesen ins Git repo committest.

Wir empfehlen deine [IDE so zu konfigurieren](https://github.com/ecamp/ecamp3/wiki/installation-development-windows#code-auto-formatting), dass dein Code beim Speichern automatisch richtig formatiert wird.

Alternativ kannst du

- php-cs-fixer und ESLint / Prettier vor jedem commit manuell laufen lassen:
  ```bash
  docker compose run api composer cs-fix
  docker compose run frontend npm run lint
  docker compose run print npm run lint
  ```
- einen git pre-commit hook einrichten, der php-cs-fixer und ESLint vor jedem commit triggert

### Vor dem Einreichen eines Pull Requests

- [x] Wurden alle geänderten oder neuen PHP-Dateien von cs-fixer verarbeitet?
- [x] Wurden alle geänderten oder neuen JS/Vue-Dateien von ESLint / Prettier bereinigt?
- [x] Sind alle Variabeln, Klassen, Funktionen, Kommentare, etc. auf englisch benannt?
- [x] Hast du für neue Funktionalität Tests geschrieben und für geänderte Funktionalität die Tests angepasst?
- [x] Wurden alle Passwörter, Zugangsdaten und lokale Konfiguration aus den Code-Änderungen entfernt?
- [x] Enthalten alle geänderten Dateien auch wirklich eine sinnvolle Änderung (im Gegensatz zu z.B. nur Whitespace-Änderungen)?
- [x] Ist der Fork auf dem aktuellen Stand des zentralen Repositories und können die Änderungen automatisch gemerged werden?
- [x] Ist der GitHub Actions CI-Build erfolgreich und ohne Test Failures durchgelaufen?
