<?php

namespace EcampCore\Acl;

interface BelongsToUser
{
	
	/**
	 * @return EcampCore\Entity\User
	 */
	function getUser();
}