<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class DayService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , DayHydrator $dayHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $dayHydrator
        , Day::class
        );
    }

    /**
     * @param mixed $data
     * @return Day|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Period $period */
        $period = $this->findEntity(Period::class, $data->period_id);

        /** @var Day $day */
        $day = parent::create($data);
        $period->addDay($day);

        return $day;
    }
}
