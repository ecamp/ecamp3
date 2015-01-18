<?php

namespace EcampMaterial\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class MaterialListRepository
    extends EntityRepository
{

    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('e');
        $q->andWhere("e.camp = :camp");
        $q->setParameter('camp', $criteria["camp"]);

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

}
