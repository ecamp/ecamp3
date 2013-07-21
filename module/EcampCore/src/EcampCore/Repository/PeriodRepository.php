<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class PeriodRepository extends EntityRepository
{

    public function findForApi(array $criteria)
    {
        $q = $this->createQueryBuilder('p');

        if (isset($criteria["offset"]) && !is_null($criteria["offset"])) {
            $q->setFirstResult($criteria["offset"]);
            $q->setMaxResults(100);
        }
        if (isset($criteria["limit"]) && !is_null($criteria["limit"])) {
            $q->setMaxResults($criteria["limit"]);
        }

        return $q->getQuery()->getResult();
    }

    public function findCampPeriods($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
