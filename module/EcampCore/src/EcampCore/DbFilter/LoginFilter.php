<?php

namespace EcampCore\DbFilter;

use EcampCore\Entity\Camp;
use EcampCore\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class LoginFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $login){
		
		if($targetEntity->getName() == 'EcampCore\Entity\Login'){
			
			$me = self::getMySqlId();
			
			return 
				"	(" .
				"		$login.user_id = $me" .
				"	)";
		}
		
		return "";
	}
	
}