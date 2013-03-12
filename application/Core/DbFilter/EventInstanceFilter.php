<?php

namespace Core\DbFilter;

use CoreApi\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class EventInstanceFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $eventInstance){
		
		if($targetEntity->getName() == 'CoreApi\Entity\EventInstance'){
			
			$me = "'" . self::getMe()->getId() . "'";
			
			$camp = self::getTablename('CoreApi\Entity\Camp');
			$event = self::getTablename('CoreApi\Entity\Event');
			$userCamp = self::getTablename('CoreApi\Entity\UserCamp');
			
			return 
				"	(" .
				"		exists(" .
				"			select 	1" .
				"			from 	$camp c" . 
				"			join	$event e on e.camp_id = c.id" .
				"			where	$eventInstance.event_id = e.id" .
				"			and		c.owner_id = $me" . 
				"		)" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from 	$userCamp uc" .
				"			join	$event e on e.camp_id = uc.camp_id" .
				"			where 	uc.user_id = $me" .
				"			and 	e.id = $eventInstance.event_id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
	
}