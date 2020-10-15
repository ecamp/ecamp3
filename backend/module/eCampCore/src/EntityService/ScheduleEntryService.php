<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ScheduleEntryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ScheduleEntryService extends AbstractEntityService {
    private ActivityService $activityService;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, ActivityService $activityService) {
        parent::__construct(
            $serviceUtils,
            ScheduleEntry::class,
            ScheduleEntryHydrator::class,
            $authenticationService
        );
        $this->activityService = $activityService;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['activityId'])) {
            $q->andWhere('row.activity = :activityId');
            $q->setParameter('activityId', $params['activityId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }

    /**
     * @param mixed $data
     *
     * @throws ORMException
     * @throws NoAccessException
     * @throws EntityNotFoundException
     *
     * @return ScheduleEntry
     */
    protected function createEntity($data) {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = parent::createEntity($data);

        // @var Period $period
        if (isset($data->periodId)) {
            $period = $this->findEntity(Period::class, $data->periodId);
            $period->addScheduleEntry($scheduleEntry);
        }

        // @var Activity $activity
        if (isset($data->activityId)) {
            $activity = $this->findEntity(Activity::class, $data->activityId);
            $activity->addScheduleEntry($scheduleEntry);
        }

        return $scheduleEntry;
    }

    /**
     * @param BaseEntity|ScheduleEntry $scheduleEntry
     * @param mixed                    $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return BaseEntity|ScheduleEntry
     */
    protected function createEntityPost(BaseEntity $scheduleEntry, $data) {
        // Create Periods:
        if (isset($data->activity)) {
            $activity = (object) $data->activity;
            if (!isset($activity->id)) {
                $activity->scheduleEntries[] = $scheduleEntry;
                $this->activityService->create($activity);
            }
        }

        return $scheduleEntry;
    }
}
