<?php

namespace Core\DbFilter;

use CoreApi\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class UserCampFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $userCamp){
		
		if($targetEntity->getName() == 'CoreApi\Entity\UserCamp'){
			
			$me = self::getMySqlId();
			
			$camp = self::getTablename('CoreApi\Entity\Camp');
			$userCampDbTblName = self::getTablename('CoreApi\Entity\UserCamp');
			
			return
				"	(" .
				"		$userCamp.user_id = $me" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from	$camp c" .
				"			where	c.id = $userCamp.camp_id" .
				"			and		c.owner_id = $me" . 
				"		)" .
				"	or" .
				"		exists(" .
				"			select 	1" .
				"			from 	$userCampDbTblName uc" .
				"			where 	uc.user_id = $me" .
				"			and 	uc.camp_id = $userCamp.camp_id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
}