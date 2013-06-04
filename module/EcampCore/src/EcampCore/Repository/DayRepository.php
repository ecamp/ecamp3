<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class DayRepository extends EntityRepository
{

    public function findPeriodDays($periodId)
    {
        return $this->findBy(array('period' => $periodId));
    }

}
