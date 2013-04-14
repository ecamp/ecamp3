<?php

namespace EcampCore\DbFilter;


use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

abstract class BaseFilter extends SQLFilter
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 */
	static private $contextProvider = null;
	
	static protected function getMySqlId(){
		if(self::$contextProvider === null){
			self::$contextProvider =
				\Zend_Registry::get('kernel')->Get('CoreApi\Acl\ContextProvider');
		}
		
		$me = self::$contextProvider->getMe();
		return ($me != null) ? "'" . $me->getId() . "'" : "null";
	}
	
	
		
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private static $entityManager = null;
	
	protected static function getEntityManager(){
		if(self::$entityManager === null){
			self::$entityManager =
				\Zend_Registry::get('kernel')->Get('Doctrine\ORM\EntityManager');
		}
		
		return self::$entityManager;
	}
	
	
	
	private static $classMetadata = array();

	protected static function addMetadata(ClassMetadata $metadata){
		if(!array_key_exists($metadata->name, self::$classMetadata)){
			self::$classMetadata = $metadata;
		}
	}
	
	/**
	 * @return \Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected static function getMetadata($name){
		if(array_key_exists($name, self::$classMetadata)){
			return self::$classMetadata[$name];
		}
		
		$metadata = self::getEntityManager()->getClassMetadata($name);
		self::addMetadata($metadata);
		
		return $metadata;
	}
	
	protected static function getTablename($name){
		return self::getMetadata($name)->getTableName();
	}
}