<?php

namespace EcampStoryboard\DbFilter;

use Doctrine\ORM\Mapping\ClassMetadata;

use EcampCore\DbFilter\BaseFilter;

class SectionFilter
	extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $section){
		
		if($targetEntity->getName() == 'EcampStoryboard\Entity\Section'){
					
			$me = self::getMySqlId();
				
			$camp = self::getTablename('EcampCore\Entity\Camp');
			$userCamp = self::getTablename('EcampCore\Entity\UserCamp');
			$pluginInstance = self::getTablename('EcampCore\Entity\PluginInstance');
			$event = self::getTablename('EcampCore\Entity\Event');
			
			return
				"	(" .
				"		exists(" .
				"			select 	1" .
				"			from 	$camp c" .
				"			join	$event e on e.camp_id = c.id" .
				"			join	$pluginInstance pi on pi.event_id = e.id" .
				"			where	$section.pluginInstance_id = pi.id" .
				"			and		c.owner_id = $me" .
				"		)" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from 	$userCamp uc" .
				"			join	$event e on e.camp_id = uc.camp_id" .
				"			join	$pluginInstance pi on pi.event_id = e.id" .
				"			where 	uc.user_id = $me" .
				"			and 	$section.pluginInstance_id = pi.id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
	
}