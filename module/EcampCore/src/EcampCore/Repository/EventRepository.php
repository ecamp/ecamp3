<?php

namespace EcampCore\Repository;


use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampCore\Entity\Event;
use Zend\Paginator\Paginator;

/**
 * Class EventRepository
 * @package EcampCore\Repository
 *
 * @method Event find($id)
 */
class EventRepository extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('e');

        if (isset($criteria['camp']) && !is_null($criteria['camp'])) {
            $q->andWhere("e.camp = :camp");
            $q->setParameter('camp', $criteria["camp"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function findByCamp($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
