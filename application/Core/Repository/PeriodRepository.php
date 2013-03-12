<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;

use CoreApi\Entity\UserCamp;
use CoreApi\Entity\User;
use CoreApi\Entity\Group;


class PeriodRepository extends EntityRepository
{
	
	public function findCampPeriods($campId){
		return $this->findBy(array('camp' => $campId));
	}

}
