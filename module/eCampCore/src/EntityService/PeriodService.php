<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\ApiProblem\ApiProblem;

class PeriodService extends AbstractEntityService {

    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Period::class,
            PeriodHydrator::class
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    /**
     * @param mixed $data
     * @return Period|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Camp $camp */
        $camp = isset($data->camp)
            ? $data->camp
            : $this->findEntity(Camp::class, $data->camp_id);

        /** @var Period $period */
        $period = parent::create($data);
        $camp->addPeriod($period);

        $durationInDays = $period->getDurationInDays();
        for ($idx = 0; $idx < $durationInDays; $idx++) {
            $this->dayService->create((object)[
                'period' => $period,
                'day_offset' => $idx
            ]);
        }

        return $period;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return Period|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function patch($id, $data) {
        /** @var Period $period */
        $period = parent::patch($id, $data);
        $this->updatePeriodDays($period);

        $moveEvents = isset($data->move_events) ? $data->move_events : null;
        $this->updateEventInstances($period, $moveEvents);

        return $period;
    }

    /**
     * @param mixed $id
     * @return bool|null|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function delete($id) {
        /** @var Period $period */
        $period = $this->fetch($id);
        $camp = $period->getCamp();
        $camp->removePeriod($period);

        return parent::delete($id);
    }

    /**
     * @param Period $period
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
            /** @var Day $day */
            $this->getDayService()->delete($day->getId());
        }

        for ($idx = 0; $idx < $daysCountNew; $idx++) {
            $day = $days->filter(function (Day $day) use ($idx) {
                return $day->getDayOffset() == $idx;
            });

            if ($day->isEmpty()) {
                $this->getDayService()->create((object)[
                    'period_id' => $period->getId(),
                    'day_offset' => $idx
                ]);
            }
        }
    }

    /**
     * @param Period $period
     * @param bool $moveEvents
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
                /** @var EventInstance $eventInstance */
                $this->getEventInstanceService()->patch($eventInstance->getId(), (object)[
                    'start' => $eventInstance->getStart() - $delta
                ]);
            }
        }
    }
}
