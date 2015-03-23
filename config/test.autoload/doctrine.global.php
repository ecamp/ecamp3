<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'user' => 'ecamp3',
                    // 'path'=> __DIR__.'/../../data/ecamp3.db',
                    'memory' => true
                ),
            ),
        ),
    ),
);
