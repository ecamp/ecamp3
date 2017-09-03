<?php
namespace EcampApi\V1\Rest\CampType;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use EcampCore\Entity\CampType;

class CampTypeHydrator extends DoctrineObject {
	
	public function hydrate(array $data, $object){
		/** @var CampType $campType */
		$campType = $object;
		
		parent::hydrate($data, $campType);
		
		// Manipulate $campType
	}
	
	
	public function extract($object){
		$data = parent::extract($object);
		
		// Manipulate $data
		$data['test'] = 'test';
		
		return $data;
	}
	
}
