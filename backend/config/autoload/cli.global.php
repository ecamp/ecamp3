<?php

return [
    'laminas-cli' => [
        'commands' => [
            'rebuild-database' => \eCamp\Lib\Command\RebuildDatabaseSchemaCommand::class,
            'update-database' => \eCamp\Lib\Command\UpdateDatabaseSchemaCommand::class,
            'load-data-fixture' => \eCamp\Lib\Command\LoadDataFixtureCommand::class,
        ],
    ],
];
