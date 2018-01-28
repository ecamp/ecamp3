<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'user' => 'ecamp3',
                    'memory' => true
                ),
            ),
        ),

        'configuration' => [
            'orm_default' => [
                'proxy_dir' => __DIR__ . '/../../data/DoctrineORMModule/Proxy',
            ],
        ],
    ),
);
