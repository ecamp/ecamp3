<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Authentication\AuthenticationService;

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

        // $moveActivities = isset($data->move_activities) ? $data->move_activities : null;
        // $this->updateScheduleEntries($period, $moveActivities);

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

        // $moveActivities = isset($data->move_activities) ? $data->move_activities : null;
        // $this->updateScheduleEntries($period, $moveActivities);

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
     * @param bool $moveActivities
     *
     * @throws NoAccessException
     */
    private function updateScheduleEntries(Period $period, $moveActivities = null) {
        if (is_null($moveActivities)) {
            $moveActivities = true;
        }

        if (!$moveActivities) {
            $start = $period->getStart();

            $origData = $this->getOrigEntityData($period);
            if (isset($origData['start'])) {
                $start = $origData['start'];
            }

            $delta = $period->getStart()->getTimestamp() - $start->getTimestamp();
            $delta = $delta / 60;

            $scheduleEntries = $period->getScheduleEntries();
            foreach ($scheduleEntries as $scheduleEntry) {
                // @var ScheduleEntry $scheduleEntry
                $this->getScheduleEntryService()->patch($scheduleEntry->getId(), (object) [
                    'start' => $scheduleEntry->getStart() - $delta,
                ]);
            }
        }
    }
}
