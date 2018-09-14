# Installation auf Ubuntu

Hier zeigen wir dir, wie du eCamp auf deinem Ubuntu installieren kannst.
Eine lokale Installation ist primär für das Entwicklen sinnvoll.

Wenn du eCamp einfach nur nutzen willst, dann empfehlen wir dir dringend die 
bereits installierte Verison auf [http://ecamp.pfadiluzern.ch](http://ecamp.pfadiluzern.ch)
zu verwenden.

## Umfang
Wir gehen davon aus, dass du Ubuntu bereits installiert hast.
Alles weitere installieren wir nun gemeinsam. 

Wir starten mit dem Server. Dafür verwenden wir XAMP.
XAMP umfasst einen Apache Web-Server sowie eine MySql Datenbank. Beide werden wir 
so konfigurieren, dass wir darauf eCamp laufen lassen können.

Anschlissend installieren wir eCamp selber. Das heisst, wir laden die Sourcen 
herunter und richten die Datenbank ein.

Das wars dann auch schon. Doch jetzt Schritt für Schritt.


## XAMPP installieren
XAMPP ist eine Sammlung von verschiedenen Produkten. Es umfasst unter anderem einen
Apache-WebServer und einen MySql-Server. Diese beiden werden wir benötigen.

Du kannst XAMPP hier herunterladen:
[https://www.apachefriends.org/](https://www.apachefriends.org/)
(Linux-Variante - mindestens Verison 7.2)

Die Installation startest du am einfachsten aus dem Terminal.
```
// Berechtigung zum Ausführen erteilen
sudo chmod 755 xampp-linux-*-installer.run

// Installieren:
sudo ./xampp-linux-*-installer.run 
```

Es öffnet sich ein Installations-Wizard. Folge seinen Anweisungen.
Installiere folgende Packete:
- XAMPP Core Files
- XAMPP Developer Files

Grundsätzlich lässt sich XAMPP nach der Installation mittels Terminal
bedienen.
```
// Start:
$ sudo /opt/lampp/xampp start

// Stop:
$ sudo /opt/lampp/xampp stop
```

Wenn du lieber mit dem GUI arbeitest, dann gehe wie folgt vor:
```
// Berechtigung zum Ausführen erteilen:
sudo chmod 755 /opt/lampp/xampp/manager-linux-x64.run

// GUI Starten:
sudo /opt/lampp/xampp/manager-linux-x64.run
```


Ob XAMPP gerade gestartet ist, kannst du mittels folgender URL testen:
[http://localhost/dashboard](http://localhost/dashboard)

Hier sollte nun einen Site von XAMPP erscheinen.
Das Dashboard hat oben rechts zwei spannende Links. 
- PHPInfo (zeigt die installierte PHP-Version)
- phpMyAdmin (Administrationstool für den MySql-Server)

### PHP Extensions
Für eCamp brauchen wir zusätzliche PHP-Extensions.
Diese kannst du mittels Terminal einfach installieren:
```
$ sudo apt install php-xml
$ sudo apt install php-mbstring
$ sudo apt install php-mysqli
```

### Datenbank einrichten

Unter [http://localhost/dashboard](http://localhost/dashboard) haben wir den Link für
phpMyAdmin gesehen. Um die Datenbank einzurichten, folgen wir diesem Link.
Im Moment verzichten darauf, hier viele Einstellungen vorzunehmen. Was wir aber 
unbedingt brauchen, ist eine Datenbank, in welche eCamp seine Daten speichern kann.

- Neu  (oben links)
- Name vergeben: eCamp3dev
- Fertig


## eCamp Sourcen
Damit wir die Sourcen von GitHub herunterladen können, brauchen wi GIT und
Composer. Falls du GIT und Composer bereits installiert hast, kannst du hier 
ein paar Zeilen überspringen.

### GIT installieren
GIT installieren ist nicht weiter schwer. Öffne das Terminal und tippe folgendes:
```
sudo apt install git
```

### Composer installieren
Composer installieren ist ebenfalls nicht weiter schwer.
```
sudo apt install composer
```

### Sourcen von GitHub laden
Die eCamp Sourcen liegen auf GitHub. Damit du die Sourcen auf deinen Rechner
heruntergeladen bekommst, musst du das Git-Repository von eCamp klonen.
Überlege dir hierfür, wo du die Sourcen auf deinem Rechner haben möchtest.
In diesem Beispeil werden wir die Sourcen unter '/var/www/ecamp3' abspeichern.
```
$ cd /var/www
$ git clone https://github.com/ecamp/ecamp3.git
```

Das eCamp3 Repository enthält ein composer.json File, welches weitere Abhängigkeiten
deklariert. Diese Abhängigkeiten müssen ebenfalls auf deinen Rechner heruntergeladen
werden. Dafür verwenden wir Composer.
```
$ cd ecamp3
$ composer install
``` 


## Apache konfigurieren

Jetzt, da wir wir alle Sourcen auf dem Rechner haben, müssen wir den WebServer noch so
einrichten, dass er diese findet und so die WebSite anzeigen kann.

### Apache vHost einrichten

Als erstes stellen wir den Apache WebServer so ein, dass er eine zusätzliche URL bedient.
Dafür sind folgende Schritte notwendig:

1) Apache stoppen
```
$ sudo /opt/lampp/manager-linux-x64.run
```

- [ Stop All ] 

2) Apache Config anpassen

```
$ sudo gedit /opt/lampp/etc/httpd.conf
```

Bei der Zeile "#Include ext/extra/httpd-vhost.conf" entfernen wir das "#" (Kommentar-Zeichen).
So wird das die httpd-vhost.conf nun berücksichtigt. Dies erlaubt es uns, zusätzliche
Virtual-Hosts zu konfigurieren.

Speichern, schlissen.


```
$ sudo gedit /opt/lampp/etc/extra/httpd.conf
```

Hier tragen wir nun unseren zusätzlichen Virtual-Host ein.
Füge hierzu folgende Zeilen ein.

```
<VirtualHost *:80>
    ServerAdmin info@ecamp3
    DocumentRoot /var/www/ecamp3/public
    ServerName ecamp3
    
    <Directory /var/www/ecamp3/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3) Apache starten
```
$ sudo /opt/lampp/manager-linux-x64.run
```

- [ Start All ] 



### Hosts einrichten

Nun bringen wir unserem Rechner noch bei, wie er die URL [http://ecamp3/](http://ecamp3) 
aufzulösen hat. Da der WebServer ebenfalls auf unserem Rechner arbeitet, sollte diese 
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
Dies sollte nun die IP-Adresse 127.0.0.1 anzeigen.



### Apache Installation überprüfen

Nun versucht folgende Adresse anzuzeigen:
[http://ecamp3/setup.php](http://ecamp3/setup.php)

Wenn alles korrekt funktioniert hat, solltest du auf der Setup-Seite von 
eCamp3 gelandet sein. Diese Seite überprüft einige der vorhergegangenen Schritte:
- Composer Install
- Datenbank-Verbindung
- Datenbank-Tabellen

Im Moment wird nur der erste Check positiv verlaufen - denn Composer haben 
wir ja bereits laufen lassen.


## eCamp Konfigurieren

Damit die Sourcen von eCamp auf deine MySql Datenbank zugreifen können, müssen
wir die Zugangs-Daten konfigurieren.
Hierfür kopieren wir die Konfigurations-Vorlage.

```
$ cp /var/www/ecamp3/config/autoload/doctrine.local.dev.dist /var/www/ecamp3/config/autoload/doctrine.local.dev.php
```

Die erstellte Datei öffnen und anpassen.

```
$ gedit /var/www/ecamp3/config/autoload/doctrine.local.dev.php
```

Wenn du beim Konfigurieren vom XAMPP keine Änderungen vorgenommen hast, sollte 
die Konfigurations-Vorlag bereits passen.
Ansonsten setzt du nun 'user' und 'password' auf die korrekten Werte.


Kontrolle: [http://ecamp3/setup.php](http://ecamp3/setup.php).
Hier sollte nun angezeigt werden, dass eCamp zum Datenbank-Server verbinden 
konnte. Die Datenbank enthält jedoch noch keine Tabellen.

Die fehlenden Tabellen lassen sich leicht erstellen:
```
$ /var/www/ecamp3/vendor/bin/doctrine orm:schema-tool:create
```
 

Kontrolle: [http://ecamp3/setup.php](http://ecamp3/setup.php).
Nun sollte alles in Ordnung sein - und wir haben die Möglichkeit ein paar Test-Daten
in die Datenbank zu laden. Klicke hierfür auf 'Load Dev-Data'.



Gratuliere. Du hast es geschaft!

Besuche nun dein eigenes eCamp:
[http://ecamp3/](http://ecamp3/)
