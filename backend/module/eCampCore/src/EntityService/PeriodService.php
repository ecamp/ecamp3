<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

class PeriodService extends AbstractEntityService {
    /** @var DayService */
    protected $dayService;

    public function __construct(
        DayService $dayService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService
    ) {
        parent::__construct(
            $serviceUtils,
            Period::class,
            PeriodHydrator::class,
            $authenticationService
        );

        $this->dayService = $dayService;
    }

    /**
     * @param mixed $data
     *
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ApiProblem|Period
     */
    public function create($data) {
        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->campId);

        /** @var Period $period */
        $period = parent::create($data);
        $camp->addPeriod($period);

        $this->assertAllowed($period, Acl::REST_PRIVILEGE_CREATE);
        $this->getServiceUtils()->emFlush();

        $durationInDays = $period->getDurationInDays();
        for ($idx = 0; $idx < $durationInDays; ++$idx) {
            $this->dayService->create((object) [
                'periodId' => $period->getId(),
                'dayOffset' => $idx,
            ]);
        }

        return $period;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|Period
     */
    public function update($id, $data) {
        /** @var Period $period */
        $period = parent::update($id, $data);
        $this->updatePeriodDays($period);

        // $moveEvents = isset($data->move_events) ? $data->move_events : null;
        // $this->updateEventInstances($period, $moveEvents);

        return $period;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|Period
     */
    public function patch($id, $data) {
        /** @var Period $period */
        $period = parent::patch($id, $data);
        $this->updatePeriodDays($period);

        // $moveEvents = isset($data->move_events) ? $data->move_events : null;
        // $this->updateEventInstances($period, $moveEvents);

        return $period;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    private function updatePeriodDays(Period $period) {
        $days = $period->getDays();
        $daysCountNew = $period->getDurationInDays();

        $daysToDelete = $days->filter(function (Day $day) use ($daysCountNew) {
            return $day->getDayOffset() >= $daysCountNew;
        });

        foreach ($daysToDelete as $day) {
            // @var Day $day
            $this->dayService->delete($day->getId());
        }

        for ($idx = 0; $idx < $daysCountNew; ++$idx) {
            $day = $days->filter(function (Day $day) use ($idx) {
                return $day->getDayOffset() == $idx;
            });

            if ($day->isEmpty()) {
                $this->dayService->create((object) [
                    'periodId' => $period->getId(),
                    'dayOffset' => $idx,
                ]);
            }
        }
    }

    /**
     * @param bool $moveEvents
     *
     * @throws NoAccessException
     */
    private function updateEventInstances(Period $period, $moveEvents = null) {
        if (is_null($moveEvents)) {
            $moveEvents = true;
        }

        if (!$moveEvents) {
            $start = $period->getStart();

            $origData = $this->getOrigEntityData($period);
            if (isset($origData['start'])) {
                $start = $origData['start'];
            }

            $delta = $period->getStart()->getTimestamp() - $start->getTimestamp();
            $delta = $delta / 60;

            $eventInstances = $period->getEventInstances();
            foreach ($eventInstances as $eventInstance) {
                // @var EventInstance $eventInstance
                $this->getEventInstanceService()->patch($eventInstance->getId(), (object) [
                    'start' => $eventInstance->getStart() - $delta,
                ]);
            }
        }
    }
}
