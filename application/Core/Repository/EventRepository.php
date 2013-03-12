<?php

namespace Core\Repository;


use CoreApi\Entity\Period;

use Doctrine\ORM\EntityRepository;

use CoreApi\Entity\Day;
use CoreApi\Entity\EventInstance;


class EventRepository extends EntityRepository
{
	
	public function findByCamp($campId){
		return $this->findBy(array('camp' => $campId));	
	}
	
}
