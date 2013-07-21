<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class EventCategoryRepository extends EntityRepository
{

    public function findForApi(array $criteria)
    {
        $q = $this->createQueryBuilder('e');

        if (isset($criteria['camp']) && !is_null($criteria['camp'])) {
            $q->andWhere("e.camp = :camp");
            $q->setParameter('camp', $criteria["camp"]);
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

    public function findByCamp($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
