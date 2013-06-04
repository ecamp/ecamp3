<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Day;

class EventInstanceRepository extends EntityRepository
{

    public function findByDay($day)
    {
        if (! $day instanceof Day) {
            $day = $this->_em->find('EcampCore\Entity\Day', $day);
        }

        if ($day != null) {
            $dayStart 	= $day->getDayOffset() * 24 * 60;
            $dayEnd		= ($day->getDayOffset() + 1) * 24 * 60;

            $q = $this->createQueryBuilder('ei')
                ->where('ei.period = :periodId')
                ->andWhere('ei.minOffsetStart < :dayEnd')
                ->andWhere('ei.minOffsetEnd > :dayStart')
                ->setParameter('periodId', $day->getPeriod()->getId())
                ->setParameter('dayEnd', $dayEnd)
                ->setParameter('dayStart', $dayStart);

            return $q->getQuery()->getResult();
        }

        return array();
    }

    public function findByPeriod($periodId)
    {
        return $this->findBy(array('period'=> $periodId));
    }

    public function findByCamp($campId)
    {
        $q = $this->createQueryBuilder('ei')
            ->join('ei.period', 'p')
            ->where('p.camp = :campId')
            ->setParameter('campId', $campId);

        return $q->getQuery()->getResult();

    }

}
