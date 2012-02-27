<?php

namespace Core\Entity\Wrapper;

class Unwrapper extends \CoreApi\Entity\BaseEntity
{
	private final function __construct(){}
	private final function __clone(){}
	
	public static function Unwrapp(\CoreApi\Entity\BaseEntity $entity)
	{
		return $entity->wrappedObject;
	}
}  