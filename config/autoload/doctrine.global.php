<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'charset'  => 'utf8',
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'eCamp3dev',
                )
            ),
        ),

        'configuration' => [
            'orm_default' => [
                'proxy_dir' => __DIR__ . '/../../data/DoctrineORMModule/Proxy',
            ],
        ],
    ),
);
