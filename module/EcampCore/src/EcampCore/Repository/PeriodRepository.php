<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class PeriodRepository extends EntityRepository
{

    public function findCampPeriods($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
