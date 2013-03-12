<?php

namespace Core\DbFilter;

use CoreApi\Entity\Camp;
use CoreApi\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class LoginFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $login){
		
		if($targetEntity->getName() == 'CoreApi\Entity\Login'){
			
			$me = "'" . self::getMe()->getId() . "'";
			
			return 
				"	(" .
				"		$login.user_id = $me" .
				"	)";
		}
		
		return "";
	}
	
}