<?php

namespace EcampCore\DbFilter;

use EcampCore\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class PeriodFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $period){
		
		if($targetEntity->getName() == 'EcampCore\Entity\Period'){
			
			$me = self::getMySqlId();
			
			$camp = self::getTablename('EcampCore\Entity\Camp');
			$userCamp = self::getTablename('EcampCore\Entity\UserCamp');
			
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