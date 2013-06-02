<?php
return array(
	
	'abstract_factories' => array(
			/**
			 * Provides repositories for all doctrine entities
			 * Pattern: EcampCore\Repository\*
			 */
			'EcampLib\Repository\AbstractRepositoryFactory',
			
			/**
			 * Provides wrapped services for all internal service classes 
			 * Pattern: EcampCore\Service\*
			 */
			'EcampLib\Service\AbstractServiceFactory',
			
			/**
			 * Provides internal services for all existing service classes 
			 * Pattern: EcampCore\Service\*\Internal
			 */
			'EcampLib\Service\AbstractInternalServiceFactory',
	),
		
	'aliases' => array(
	),
	
	'factories' => array(
		'EcampCore\Acl' => 'EcampCore\Acl\AclFactory',
	),
	
	'invokables' => array(
		'EcampCore\Service\Period\Internal' => 'EcampCore\Service\PeriodService',
	),
	
	'initializers' => array(
	),
);