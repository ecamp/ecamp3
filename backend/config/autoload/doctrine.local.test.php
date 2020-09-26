<?php

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => [
                    'user' => 'ecamp3',
                    'memory' => true,
                ],
            ],
        ],
    ],
];
