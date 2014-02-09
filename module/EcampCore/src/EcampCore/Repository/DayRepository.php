<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use EcampCore\Entity\Day;

class DayRepository extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        /**
        TBD: standard order by date
         */
        $q = $this->createQueryBuilder('d');

        if (isset($criteria['period']) && !is_null($criteria['period'])) {
            $q->andWhere("d.period = :period");
            $q->setParameter('period', $criteria["period"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findNextDay(Day $day)
    {
        $period = $day->getPeriod();

        $nextOffset = $day->getDayOffset() + 1;

        if ($nextOffset < $period->getDays()->count()) {
            return $period->getDays()->get($nextOffset);
        } else {
            $camp = $day->getCamp();
            $nextPeriod = $camp->getPeriods()->indexOf($period) + 1;

            if ($nextPeriod < $camp->getPeriods()->count()) {
                return $camp->getPeriods()->get($nextPeriod)->getDays()->first();
            }
        }

        return null;
    }

    public function findPrevDay(Day $day)
    {
        $period = $day->getPeriod();

        $prevOffset = $day->getDayOffset() - 1;

        if ($prevOffset >= 0) {
            return $period->getDays()->get($prevOffset);
        } else {
            $camp = $day->getCamp();
            $prevPeriod = $camp->getPeriods()->indexOf($period) - 1;

            if ($prevPeriod >= 0) {
                return $camp->getPeriods()->get($prevPeriod)->getDays()->last();
            }
        }

        return null;
    }

    public function findPeriodDays($periodId)
    {
        return $this->findBy(array('period' => $periodId));
    }

}
