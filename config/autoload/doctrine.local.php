<?php

return array(
	'doctrine' => array(
		'connection' => array(
			'orm_default' => array(
				'driverClass' => 'Doctrine\DBAL\Driver\PDOMysql\Driver',
				
				'params' => array(
					'host' 		=> 'localhost',
					'port'		=> '3306',
					'user'		=> 'root',
					'password'	=> 'root',
					'dbname'	=> 'eCamp3dev'
				)
			)
		)
	)
);
