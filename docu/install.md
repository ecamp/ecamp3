# Installation
## Voraussetzungen

- GIT
- Composer
- WebServer
  - Apache
  - Nginx
  - WAMP
  - MAMP
  - ...
- Datenbank
  - MySql
  - ...

## Instruktion
1) Repository clonen ``` git clone https://github.com/ecamp/ecamp3.git ```
2) Abhängigkeiten installieren ``` composer install ```
3) DB-Konfiguration erfassen:  
   Copy ```/config/autoload/doctrine.local.prod.dist``` to 
   ```/config/autoload/doctrine.local.prod.php```
4) DB-Zugangs-Daten in ```/config/autoload/doctrine.local.prod.php``` setzen. 
5) Leeres Datenbank-Schema erzeugen (z.B. mit phpMyAdmin).
6) Tabellen erzeugen: ```vendor/bin/doctrine orm:schema-tool:create```
7) Tabellen mit Stammdaten befüllen:  ```php cli-setup.php dev```
8) Seite besuchen: http://HOST/web
