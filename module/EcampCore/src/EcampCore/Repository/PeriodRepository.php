<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampCore\Entity\Period;
use Zend\Paginator\Paginator;

/**
 * Class PeriodRepository
 * @package EcampCore\Repository
 *
 * @method Period find($id)
 */
class PeriodRepository extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('p');

        if (isset($criteria['camp']) && !is_null($criteria['camp'])) {
            $q->andWhere("p.camp = :camp");
            $q->setParameter('camp', $criteria["camp"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findCampPeriods($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
