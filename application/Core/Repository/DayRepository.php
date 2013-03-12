<?php

namespace Core\Repository;

use Doctrine\ORM\EntityRepository;

use CoreApi\Entity\UserCamp;
use CoreApi\Entity\User;
use CoreApi\Entity\Group;


class DayRepository extends EntityRepository
{
	
	public function findPeriodDays($periodId){
		return $this->findBy(array('period' => $periodId));
	}

}
