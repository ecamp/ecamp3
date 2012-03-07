<?php

namespace CoreApi\Entity;

abstract class BaseEntity
{
	protected $wrappedObject;
	
	protected function getWrappedObject()
	{
		return $this->wrappedObject;
	}
}