<?php

namespace EcampCore\Acl;

interface BelongsToParentResource{
	/**
	 * @return \EcampLib\Entity\BaseEntity
	 */
	public function getParentResource();
}