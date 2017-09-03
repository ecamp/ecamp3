<?php
namespace EcampApi\V1\Rest\CampType;

use DoctrineModule\Stdlib\Hydrator\Filter\PropertyName;

class CampTypeFilter extends PropertyName{
	
	public function __construct() {
		parent::__construct(['name']);
	}
	
}
