<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class PeriodService extends BaseService
{
    private $dayService;

    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , DayService $dayService
    ) {
        parent::__construct($acl, $entityManager, Period::class, PeriodHydrator::class);

        $this->dayService = $dayService;
    }

    /**
     * @param mixed $data
     * @return Period|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->camp_id);

        /** @var Period $period */
        $period = parent::create($data);
        $camp->addPeriod($period);

        $durationInDays = $period->getDurationInDays();
        for ($idx = 0; $idx < $durationInDays; $idx++) {
            $this->dayService->create((object)[
                'period_id' => $period->getId(),
                'day_offset' => $idx
            ]);
        }

        return $period;
    }

}
