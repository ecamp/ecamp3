<?php

namespace EcampCore\Acl;

interface BelongsToCamp
{
	
	/**
	 * @return EcampCore\Entity\Camp
	 */
	function getCamp();
}