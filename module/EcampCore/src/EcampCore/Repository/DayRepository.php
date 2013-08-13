<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

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

    public function findPeriodDays($periodId)
    {
        return $this->findBy(array('period' => $periodId));
    }

}
