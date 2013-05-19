<?php

namespace EcampCore\Acl;

interface BelongsToGroup
{
	
	/**
	 * @return EcampCore\Entity\Group
	 */
	function getGroup();
}