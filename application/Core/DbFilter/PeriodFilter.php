<?php

namespace Core\DbFilter;

use CoreApi\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class PeriodFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $period){
		
		if($targetEntity->getName() == 'CoreApi\Entity\Period'){
			
			$me = "'" . self::getMe()->getId() . "'";
			
			$camp = self::getTablename('CoreApi\Entity\Camp');
			$userCamp = self::getTablename('CoreApi\Entity\UserCamp');
			
			return 
				"	(" .
				"		exists(" .
				"			select 	1" .
				"			from 	$camp c" . 
				"			where	$period.camp_id = c.id" .
				"			and		c.owner_id = $me" . 
				"		)" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from 	$userCamp uc" .
				"			where 	uc.user_id = $me" .
				"			and 	uc.camp_id = $period.camp_id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
	
}