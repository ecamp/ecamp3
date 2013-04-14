Doctrine Tools
==============

Introduction
------------

This module provides an unified endpoint for Doctrine command line tools.

Installation
------------

First, add the following line into your `composer.json` file:

```json
"require": {
	"sundrique/doctrine-tools": ">=0.2"
}
```

Then, enable the module by adding `DoctrineTools` in your application.config.php file.

```php
<?php
return array(
	'modules' => array(
		'DoctrineTools',
		'DoctrineModule',
		'DoctrineORMModule',
		'Application',
	),
);
```

Create directory `data/DoctrineTools/Migrations` and make sure your application has write access to it.

Configuration
-------------

If you have already configured [DoctrineORMModule](http://www.github.com/doctrine/DoctrineORMModule) no extra configuration required. Otherwise you need to configure it first.

You can also overwrite default directory where migrations files will be stored, migrations namespace and migrations table name.

```php
<?php
return array(
    'doctrinetools' => array(
		'migrations' => array(
			'directory' => 'path/to/MyDoctrineMigrations',
			'namespace' => 'MyDoctrineMigrations',
			'table' => 'my_migrations'
		)
	)
);
```

Usage
-----

```sh
$ /path/to/your/app/public/index.php <command>
```

Available commands
------------------

The following commands are currently available:

* `help` Displays help for a command.
* `list` Lists commands.

### DBAL

* `dbal:import` Import SQL file(s) directly to Database.
* `dbal:run-sql` Executes arbitrary SQL directly from the command line.

### Migrations

* `migrations:diff` Generate a migration by comparing your current database to your mapping information.
* `migrations:execute` Execute a single migration version up or down manually.
* `migrations:generate` Generate a blank migration class.
* `migrations:migrate` Execute a migration to a specified version or the latest available version.
* `migrations:status` View the status of a set of migrations.
* `migrations:version` Manually add and delete migration versions from the version table.

### ORM

* `orm:clear-cache:metadata` Clear all metadata cache of the various cache drivers.
* `orm:clear-cache:query` Clear all query cache of the various cache drivers.
* `orm:clear-cache:result` Clear all result cache of the various cache drivers.
* `orm:convert-d1-schema` Converts Doctrine 1.X schema into a Doctrine 2.X schema.
* `orm:convert-mapping` Convert mapping information between supported formats.
* `orm:ensure-production-settings` Verify that Doctrine is properly configured for a production environment.
* `orm:generate-entities` Generate entity classes and method stubs from your mapping information.
* `orm:generate-proxies` Generates proxy classes for entity classes.
* `orm:generate-repositories` Generate repository classes from your mapping information.
* `orm:info` Show basic information about all mapped entities
* `orm:run-dql` Executes arbitrary DQL directly from the command line.
* `orm:schema-tool:create` Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.
* `orm:schema-tool:drop` Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output.
* `orm:schema-tool:update` Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata.
* `orm:validate-schema` Validate the mapping files.
