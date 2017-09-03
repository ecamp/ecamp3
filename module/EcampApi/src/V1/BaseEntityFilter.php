<?php

namespace EcampApi\V1;

use DoctrineModule\Stdlib\Hydrator\Filter\PropertyName;

class BaseEntityFilter extends PropertyName
{
	public function __construct() {
		parent::__construct(['createdAt', 'updatedAt']);
	}
}