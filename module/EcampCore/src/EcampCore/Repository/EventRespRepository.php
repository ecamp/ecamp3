<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class EventRespRepository extends EntityRepository
{
    public function findForApi(array $criteria)
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

        if (isset($criteria["offset"]) && !is_null($criteria["offset"])) {
            $q->setFirstResult($criteria["offset"]);
            $q->setMaxResults(100);
        }
        if (isset($criteria["limit"]) && !is_null($criteria["limit"])) {
            $q->setMaxResults($criteria["limit"]);
        }

        return $q->getQuery()->getResult();
    }
}
