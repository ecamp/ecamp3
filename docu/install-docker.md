# Installation mit Docker

Hier zeigen wir dir, wie du eCamp auf deinem Computer am einfachsten installieren kannst.
Eine lokale Installation ist primär für das Entwicklen sinnvoll.

Wenn du eCamp einfach nur nutzen willst, dann empfehlen wir dir dringend die bereits installierte Version auf [http://ecamp.pfadiluzern.ch](http://ecamp.pfadiluzern.ch) zu verwenden.

Falls du bei der Installation auf Probleme stösst, etwas nicht funktioniert oder du weitere Unterstützung oder Erklärungen benötigst, eröffne eine [Discussion](https://github.com/ecamp/ecamp3/discussions) auf GitHub.

Docker ist ein Tool, das dabei hilft, die gleiche Software auf ganz verschiedenen Computern laufen zu lassen. In sogenannten Docker containers kann man genau definieren, welche Software und Systemeinstellungen verfügbar sind und die containers so überall mit den gleichen Voraussetzungen laufen lassen. So müssen wir uns z.B. nicht darum kümmern, ob PHP auf unseren Computern installiert ist und in welcher Version, denn die korrekte PHP-Version ist im container installiert.

Um den öffentlich verfügbaren Programmcode von eCamp v3 auf deinen Computer herunterzuladen, brauchen wir das Tool Git. Git hilft uns dabei, wenn wir den Code ändern und verbessern, den Überblick nicht zu verlieren und behält immer Backups von allen Versionen.

## Umfang
Um eCamp bei dir laufen zu lassen musst du nur Docker (inklusive docker-compose) und Git auf deinem Computer haben.

Anleitungen um Git auf Linux, Mac OS und Windows zu installieren findest du hier: [https://git-scm.com/book/en/v2/Getting-Started-Installing-Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

Für Docker (und das dazugehörige Tool docker-compose) kannst du dieser Anleitung folgen: [https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/). Wenn du auf Linux arbeitest, musst du zuerst noch separat [Docker für Linux](https://docs.docker.com/install/#server) installieren, und du solltest Docker so [einrichten](https://docs.docker.com/engine/install/linux-postinstall) dass du ihn also non-root User bedienen kannst (also ohne `sudo`).

Wenn du diese beiden Voraussetzungen hast, können wir eCamp selber installieren. Das heisst, wir laden den Quellcode mithilfe von Git aus dem Internet ([GitHub](https://github.com/ecamp/ecamp3)) herunter und lassen die darin definierten Docker container laufen.

Das wars dann auch schon. Doch jetzt Schritt für Schritt.


## eCamp-Quellcode
Der eCamp-Programmcode liegt auf GitHub. Damit du die Dateien auf deinen Rechner heruntergeladen bekommst, musst du das Git-Repository von eCamp klonen.

Der entsprechende Befehl, falls du auf der Konsole arbeitest, sieht so aus:
```
git clone https://github.com/ecamp/ecamp3.git && cd ecamp3
```

Falls du auf Linux arbeitest und dein User-Account nicht die übliche ID 1000 hat (die ID findest du mit `id -u` heraus), musst du nun eine Kopie der Datei .env.ci namens .env erstellen, und darin deine Benutzer-ID eintragen. Das ist nötig, damit jegliche in den Containern erstellte Dateien deinem Benutzer gehören.

## Docker container starten
Das eCamp3 Repository enthält ein docker-compose.yml File, welches die benötigten Docker containers beschreibt.

Du kannst die container alle mit einem Befehl installieren und starten:
```
docker-compose up
```

Das braucht eine Internetverbindung und kann einige Minuten dauern, da alle Software von anderen Herstellern die eCamp benötigt noch heruntergeladen werden muss. Wenn sich das ganze beruhigt hat, läuft eCamp v3 auf deinem Computer. Du kannst es unter [http://localhost:3000](http://localhost:3000) aufrufen. Die Datenbank kannst du unter [http://localhost:3002](http://localhost:3002) begutachten, wenn du dich mit ecamp3 / ecamp3 einloggst. Das Backend, welches die Daten für die Webseite aus der Datenbank holt, kannst du unter [http://localhost:3001/api](http://localhost:3001/api) anschauen.

Unter [http://localhost:3001/setup.php](http://localhost:3001/setup.php) hast du die Möglichkeit ein paar Test-Daten in die Datenbank zu laden.
Klicke hierfür auf 'Load Dev-Data'.
Tipp: Wenn du dich noch vorher unter http://localhost:3000 registriert und/oder eingeloggt hast wird `setup.php` deinen User gleich in den Test-Lagern als Leitungsperson einsetzen.

Gratuliere. Du hast es geschafft!


## Composer und NPM bedienen
Für die Installation und Verwaltung von benötigter Drittsoftware verwenden wir in eCamp v3 im backend Composer und im Frontend NPM. Während der Entwicklung muss man manchmal Drittsoftware updaten oder neu installieren. Dafür muss man Composer und NPM in den Containern bedienen. Dies sieht z.B. so aus:
```
docker-compose exec backend composer update
docker-compose exec frontend npm update
```

Die Tools funktionieren also grundsätzlich genau gleich wie wenn man sie ausserhalb des Containers installiert hat, nur dass man den Befehl in einen Container hinein absetzen muss (mit `docker-compose exec <service-name>`).

## VS Code
Falls du VS Code als deinen Editor einsetzen möchtest, haben wir ein paar Informationen dazu im [wiki](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows#setting-up-the-ide) zusammengetragen (nur englisch).
