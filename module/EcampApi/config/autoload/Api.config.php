<?php

use Zend\ServiceManager\Factory\InvokableFactory;

return [
	
    'zf-versioning' => [
    ],
	
	'service_manager' => [
		'factories' => [
			\EcampApi\V1\BaseEntityFilter::class => InvokableFactory::class
		],
	],
	
];
