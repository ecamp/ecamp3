<?php

namespace CoreApi\Entity;

abstract class BaseEntity
{
	protected $wrappedObject;
	
	protected function getWrappedObject()
	{
		return $this->wrappedObject;
	}
	
	
	public function __toString()
	{
		return "[" . get_class($this) . " #" . $this->wrappedObject->getId() . "]";
	}
}