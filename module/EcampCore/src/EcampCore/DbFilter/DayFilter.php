<?php

namespace EcampCore\DbFilter;

use EcampCore\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class DayFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $day){
		
		if($targetEntity->getName() == 'EcampCore\Entity\Day'){
			
			$me = self::getMySqlId();
			
			$period = self::getTablename('EcampCore\Entity\Period');
			$camp = self::getTablename('EcampCore\Entity\Camp');
			$userCamp = self::getTablename('EcampCore\Entity\UserCamp');
			
			return 
				"	(" .
				"		exists(" .
				"			select 	1" .
				"			from 	$camp c" . 
				"			join 	$period p on p.camp_id = c.id" .
				"			where	$day.period_id = p.id" .
				"			and		c.owner_id = $me" . 
				"		)" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from 	$userCamp uc" .
				"			join	$period p on p.camp_id = uc.camp_id" .
				"			where 	uc.user_id = $me" .
				"			and 	p.id = $day.period_id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
	
}