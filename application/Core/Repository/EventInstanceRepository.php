<?php

namespace Core\Repository;


use CoreApi\Entity\Period;

use Doctrine\ORM\EntityRepository;

use CoreApi\Entity\Day;
use CoreApi\Entity\EventInstance;


class EventInstanceRepository extends EntityRepository
{
	
	public function findByDay(Day $day)
	{
		$dayStart 	= $day->getDayOffset() * 24 * 60;
		$dayEnd		= ($day->getDayOffset() + 1) * 24 * 60;
		
		$q = $this->createQueryBuilder('ei')
			->where('ei.period_id = :periodId')
			->andWhere('ei.minOffset < :dayEnd')
			->andWhere('(ei.minOffset + ei.duration) > :dayStart')
			->setParameter('periodId', $day->getPeriod()->getId())
			->setParameter('dayEnd', $dayEnd)
			->setParameter('dayStart', $dayStart);
			
		return $q->getQuery()->getResult();
	}
	
	
	public function findByPeriod(Period $period)
	{
		$q = $this->createQueryBuilder('ei')
			->where('ei.period_id = :periodId')
			->setParameter('periodId', $day->getPeriod()->getId());
			
		return $q->getQuery()->getResult();
	}
	
}
