# Installation
## Requirements

- GIT
- Composer
- WebServer
  - Apache
  - Nginx
  - WAMP
  - MAMP
  - ...
- Database
  - MySql
  - ...


## Instructions:
1) Clone repository ``` git clone https://github.com/ecamp/ecamp3.git ```
2) Install dependencies ``` composer install ```
3) Create DB-Configuration-File:  
   Copy ```/config/autoload/doctrine.local.dev.dist``` to 
   ```/config/autoload/doctrine.local.dev.php```
4) Change DB-Configuration in ```/config/autoload/doctrine.local.dev.php```. 
5) Create new empty Databse-Schema (using phpMyAdmin).
6) Create tables: ```vendor/bin/doctrine orm:schema-tool:create```
7) Visit Setup: http://localhost/setup.php
8) Load data into database 
9) Visit WebSite: http://localhost/

Replace ```localhost``` depending on your WebServer-Setup.


## WebServer
If you do not have a WebServer, you can use the built-in WebServer from PHP.
```php -S 0.0.0.0:8000 public public\server.php```

Visite WebSite: 
```http://localhost:8000/```
