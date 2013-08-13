<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class EventRespRepository extends EntityRepository
{
    public function getCollection(array $criteria)
    {
        $q = $this->createQueryBuilder('er');

        if (isset($criteria["event"]) && !is_null($criteria["event"])) {
            $q->andWhere('er.event = :event');
            $q->setParameter('event', $criteria["event"]);
        }

        if (isset($criteria["user"]) && !is_null($criteria["user"])) {
            $q->join('er.campCollaboration', 'c');
            $q->andWhere('c.user = :user');
            $q->setParameter('user', $criteria["user"]);
        }

        if (isset($criteria["collaboration"]) && !is_null($criteria["collaboration"])) {
            $q->andWhere('er.campCollaboration = :collaboration');
            $q->setParameter('collaboration', $criteria["collaboration"]);
        }
        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }
}
