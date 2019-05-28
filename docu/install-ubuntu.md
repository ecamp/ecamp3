# Installation auf Ubuntu

Hier zeigen wir dir, wie du eCamp auf deinem Ubuntu installieren kannst.
Eine lokale Installation ist primär für das Entwicklen sinnvoll.
Zudem ist es wohl für den Einstieg einfacher, die [Docker-Variante](./install-docker.md) dieser Anleitung zu befolgen.
Wenn du trotzdem eCamp ohne Docker auf Ubuntu installieren möchtest, sind hier die nötigen Schritte beschrieben.

Wenn du eCamp einfach nur nutzen willst, dann empfehlen wir dir dringend die bereits installierte Verison auf [http://ecamp.pfadiluzern.ch](http://ecamp.pfadiluzern.ch) zu verwenden.

## Umfang
Wir gehen davon aus, dass du Ubuntu bereits installiert hast.
Alles weitere installieren wir nun gemeinsam. 

Wir starten mit dem Backend-Server. Dafür verwenden wir XAMPP.
XAMP umfasst einen Apache Web-Server sowie eine MySQL Datenbank. Beide werden wir 
so konfigurieren, dass wir darauf eCamp laufen lassen können.

Anschliessend installieren wir eCamp selber. Das heisst, wir laden den Quellcode 
herunter und richten die Datenbank und das Frontend ein.

Das wars dann auch schon. Doch jetzt Schritt für Schritt.


## XAMPP installieren
XAMPP ist eine Sammlung von verschiedenen Produkten. Es umfasst unter anderem einen
Apache-WebServer und einen MySql-Server. Diese beiden werden wir benötigen.

Du kannst XAMPP hier herunterladen:
[https://www.apachefriends.org/](https://www.apachefriends.org/)
(Linux-Variante - mindestens Verison 7.2)

Die Installation startest du am einfachsten aus dem Terminal.
```
# Berechtigung zum Ausführen erteilen
$ sudo chmod 755 xampp-linux-*-installer.run

# Installieren:
$ sudo ./xampp-linux-*-installer.run
```

Es öffnet sich ein Installations-Wizard. Folge seinen Anweisungen.
Installiere folgende Packete:
- XAMPP Core Files
- XAMPP Developer Files

Grundsätzlich lässt sich XAMPP nach der Installation mittels Terminal
bedienen.
```
# Start:
$ sudo /opt/lampp/xampp start

# Stop:
$ sudo /opt/lampp/xampp stop
```

Wenn du lieber mit dem GUI arbeitest, dann gehe wie folgt vor:
```
# Berechtigung zum Ausführen erteilen:
$ sudo chmod 755 /opt/lampp/xampp/manager-linux-x64.run

# GUI Starten:
$ sudo /opt/lampp/xampp/manager-linux-x64.run
```


Ob XAMPP gerade gestartet ist, kannst du mittels folgender URL testen:
[http://localhost/dashboard](http://localhost/dashboard)

Hier sollte nun eine Seite von XAMPP erscheinen.
Das Dashboard hat oben rechts zwei spannende Links. 
- PHPInfo (zeigt die installierte PHP-Version)
- phpMyAdmin (Administrationstool für den MySQL-Server)

### PHP Extensions
Für eCamp brauchen wir zusätzliche PHP-Extensions.
Diese kannst du mittels Terminal einfach installieren:
```
$ sudo apt-get install -y php-xml php-mbstring php-mysqli
```

### Datenbank einrichten

Unter [http://localhost/dashboard](http://localhost/dashboard) haben wir den Link für
phpMyAdmin gesehen. Um die Datenbank einzurichten, folgen wir diesem Link.
Im Moment verzichten darauf, hier viele Einstellungen vorzunehmen. Was wir aber 
unbedingt brauchen, ist eine Datenbank, in welche eCamp seine Daten speichern kann.

- Neu  (oben links)
- Name vergeben: ecamp3dev
- Zeichensatz: utf8_general_ci
- Fertig


## eCamp Quellcode
Damit wir den Quellcode von GitHub herunterladen können, brauchen wir git und
Composer. Falls du git und Composer bereits installiert hast, kannst du hier 
ein paar Zeilen überspringen.

### git installieren
git installieren ist nicht weiter schwer. Tippe im Terminal folgendes:
```
$ sudo apt-get install -y git
```

### Composer installieren
Bei Composer muss man etwas mehr Aufwand betreiben, damit man danach wirklich die richtige Version hat.
```
$ sudo apt-get install -y wget
$ sudo wget -q -O- https://getcomposer.org/installer | /opt/lampp/bin/php
$ sudo ln -s /opt/lampp/bin/php /usr/local/bin/php
$ sudo mv composer.phar /usr/local/bin/composer
```

### Quellcode von GitHub laden
Der Programmcode von eCamp ist öffentlich auf GitHub abgelegt. Damit du den Code auf deinen Rechner
heruntergeladen bekommst, musst du das Git-Repository von eCamp3 klonen.
Überlege dir hierfür, wo du den Quellcode auf deinem Rechner haben möchtest.
In diesem Beispiel werden wir den Code unter '/var/www/ecamp3' abspeichern.
```
$ sudo mkdir -p /var/www
$ cd /var/www
$ sudo chown $USER .
$ git clone https://github.com/ecamp/ecamp3.git
```

Das eCamp3 Repository enthält ein composer.json File, welches weitere Abhängigkeiten
deklariert. Diese Abhängigkeiten müssen ebenfalls auf deinen Rechner heruntergeladen
werden. Dafür verwenden wir Composer.
```
$ cd ecamp3/backend
$ composer install
```

Falls composer nachfragt, wie folgt antworten:
```
> Please select which config file you wish to inject 'Zend\Validator' into:
0
> Remember this option for other packages of the same type? (Y/n)
y
```


## Apache konfigurieren

Jetzt, da wir wir den Quellcode auf dem Rechner haben, müssen wir den Webserver noch so
einrichten, dass er diesen findet und so die Webseite anzeigen kann.

### Apache vHost einrichten

Als erstes richten wir den Apache WebServer so ein, dass er eine zusätzliche URL bedient.
Dafür sind folgende Schritte notwendig:

1) Apache stoppen
```
$ sudo /opt/lampp/xampp stop
```

2) Apache Config anpassen

```
$ sudo gedit /opt/lampp/etc/httpd.conf
```

Bei der Zeile "#Include etc/extra/httpd-vhosts.conf" (fast am Ende der Datei) entfernen wir das "#" (Kommentar-Zeichen).
So wird die httpd-vhosts.conf nun berücksichtigt. Dies erlaubt es uns, zusätzliche Virtual-Hosts zu konfigurieren.

Ausserdem machen wir noch eine neue Zeile hinein, mit der wir den Port 3001 öffnen (darunter wird das Backend laufen).

```
Include etc/extra/httpd-vhosts.conf
Listen 3001
```

Speichern, schliessen.


```
$ sudo gedit /opt/lampp/etc/extra/httpd-vhosts.conf
```

Hier tragen wir nun unseren zusätzlichen Virtual-Host ein.
Ersetze hierzu den Inhalt mit folgenden Zeilen:

```
<VirtualHost *:3001>
    ServerAdmin info@ecamp3
    DocumentRoot /var/www/ecamp3/backend/public
    ServerName ecamp3
    
    <Directory /var/www/ecamp3/backend/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3) Apache starten
```
$ sudo /opt/lampp/xampp start
```



### Hosts einrichten

Nun bringen wir unserem Rechner noch bei, wie er die URL [http://ecamp3/](http://ecamp3) 
aufzulösen hat. Da der Webserver ebenfalls auf unserem Rechner arbeitet, sollte diese 
Adresse mit der IP-Adresse 127.0.0.1 aufgelöst werden.

Wir tragen die Adresse daher im Hosts-File ein.

```
$ sudo gedit /etc/hosts
```

Hier fügen wir folgende Zeile ein:
```
127.0.0.1   ecamp3
```

Speichern, schliessen.

Du kannst den Eintrag mit einem PING überprüfen.
```
$ ping ecamp3
```
Dies sollte nun die IP-Adresse 127.0.0.1 anzeigen. Mit Ctrl+C kannst du ping wieder beenden.



### Apache Installation überprüfen

Nun versuche im Browser folgende Adresse anzuzeigen:
[http://ecamp3:3001/setup.php](http://ecamp3:3001/setup.php)

Wenn alles korrekt funktioniert hat, solltest du auf der Setup-Seite von 
eCamp3 gelandet sein. Diese Seite überprüft einige der vorhergegangenen Schritte:
- Composer Install
- Datenbank-Verbindung
- Datenbank-Tabellen

Im Moment wird nur der erste Check positiv verlaufen - denn Composer haben 
wir ja bereits laufen lassen.


## eCamp Backend konfigurieren

Damit die Sourcen von eCamp auf deine MySQL Datenbank zugreifen können, müssen
wir die Zugangsdaten konfigurieren.
Hierfür kopieren wir die Konfigurations-Vorlage.

```
$ cd /var/www/ecamp3/backend/config/autoload
$ cp doctrine.local.dev.dist doctrine.local.dev.php
```


Wenn du beim Konfigurieren vom XAMPP keine Änderungen vorgenommen hast, sollte 
die Konfigurations-Vorlage bereits passen.
Ansonsten setzt du nun 'user' und 'password' auf die korrekten Werte. Dazu die erstellte Datei öffnen und anpassen.

```
$ gedit doctrine.local.dev.php
```


Kontrolle: [http://ecamp3:3001/setup.php](http://ecamp3:3001/setup.php).
Hier sollte nun angezeigt werden, dass eCamp zum Datenbank-Server verbinden 
konnte. Die Datenbank enthält jedoch noch keine Tabellen.

Die fehlenden Tabellen lassen sich leicht mit einem Befehl erstellen. Dieser braucht aber zuerst noch Schreibzugriff auf einen bestimmten Ordner.
```
$ cd /var/www/ecamp3/backend
$ sudo chmod -R a+w data
$ vendor/bin/doctrine orm:schema-tool:create
```


Kontrolle: [http://ecamp3:3001/setup.php](http://ecamp3:3001/setup.php).
Nun sollte alles in Ordnung sein - und wir haben die Möglichkeit ein paar Test-Daten
in die Datenbank zu laden. Klicke hierfür auf 'Load Dev-Data'.

## eCamp Frontend

Das nun installierte Backend kümmert sich um die Datenspeicherung, Zugriffsberechtigungen, etc., nun brauchen wir nur noch ein Frontend, welches diese Daten schön anzeigen kann.
Dazu brauchst du Node.js auf deinem Rechner.

```
$ cd ~
$ wget -q -O- https://deb.nodesource.com/setup_10.x | sudo -E bash -
$ sudo apt-get install -y nodejs
```

Nun kannst du im frontend-Ordner die Frontend-Dependencies installieren lassen:

```
$ cd /var/www/ecamp3/frontend
$ npm install
```

Ähnlich wie wir dem Backend mit doctrine.local.dev.php sagen mussten, wie es die Datenbank erreichen kann, müssen wir jetzt dem Frontend mit .env.local noch sagen, wo das Backend ist.
Kopiere dazu die vorgefertigte .env.local-Beispieldatei:

```
$ cp .env.local.dist .env.local
```

Schliesslich musst du nur noch das Frontend starten:

```
$ npm run serve
```


Gratuliere. Du hast es geschaft!

Besuche nun dein eigenes eCamp:
[http://ecamp3:3000/](http://ecamp3:3000/)
