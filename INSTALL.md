
eCamp3 - Install
=======================

Requirements:
-------------
* [GIT](https://git-scm.com/)
* [PHP 5.4](https://secure.php.net/)
* [Bower](http://bower.io/)
* [MySql](https://www.mysql.de/)



Clone Repository:
-----------------
```
git clone https://github.com/ecamp/ecamp3.git
```

```
cd ecamp3
```


Install PHP Dependencies:
-------------------------
```
php composer.phar self-update
```

```
php composer.phar install
```


Install JS/CSS Dependencies:
----------------------------
```
bower install
```


Configuration:
--------------

Connect to your database (for example with [phpmyadmin](https://www.phpmyadmin.net/)).
Create a new empty database.


Copy file [config/autoload/doctrine.common.prod.php](config/autoload/doctrine.common.prod.php) and rename it to 
[config/autoload/doctrine.common.prod.local.php](config/autoload/doctrine.common.prod.local.php).
Open the new file [config/autoload/doctrine.common.prod.local.php](config/autoload/doctrine.common.prod.local.php)
and update it with your own database settings.

You find some details here: [https://github.com/doctrine/DoctrineORMModule#connection-settings](https://github.com/doctrine/DoctrineORMModule#connection-settings)



Setup:
------

Run a PHP-Server with the following command:

```
cd public 
```

```
php -S localhost:8080 server.php
```


Visit the following url to create/update the tables in your database:
http://localhost:8080/db

Click on ``Create Schema`` and on ``Replace (Delete and import) Prod``
This should enable you to log in with User: ``admin`` and Password: ``admin``

Log in: http://localhost:8080/web/en/login

