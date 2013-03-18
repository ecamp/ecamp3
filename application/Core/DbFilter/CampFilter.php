<?php

namespace Core\DbFilter;

use CoreApi\Entity\Camp;
use CoreApi\Entity\UserCamp;
use Doctrine\ORM\Mapping\ClassMetaData;

class CampFilter extends BaseFilter
{
	
	public function addFilterConstraint(ClassMetadata $targetEntity, $camp){
		
		if($targetEntity->getName() == 'CoreApi\Entity\Camp'){
			
			$me = self::getMySqlId();
			$userCamp = self::getTablename('CoreApi\Entity\UserCamp');
			
			return 
				"	(" .
				"		$camp.visibility = '" . Camp::VISIBILITY_PUBLIC . "'" .
				"	or" . 
				"		$camp.owner_id = $me" .
				"	or" . 
				"		exists(" .
				"			select 	1" .
				"			from 	$userCamp uc" .
				"			where 	uc.user_id = $me" .
				"			and 	uc.camp_id = $camp.id" .
				"			and		uc.role > " . UserCamp::ROLE_NONE . 
				"		)".
				"	)";
		}
		
		return "";
	}
	
}