<?php

namespace EcampMaterial\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class MaterialItemRepository
    extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('e');

        if (isset($criteria['eventPlugin']) && !is_null($criteria['eventPlugin'])) {
            $q->andWhere("e.eventPlugin = :eventPlugin");
            $q->setParameter('eventPlugin', $criteria["eventPlugin"]);
        }

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

}
