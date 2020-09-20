<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ScheduleEntryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ScheduleEntryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ScheduleEntry::class,
            ScheduleEntryHydrator::class,
            $authenticationService
        );
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
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ScheduleEntry
     */
    protected function createEntity($data) {
        // @var Period $period
        if (isset($data->periodId)) {
            $period = $this->findEntity(Period::class, $data->periodId);
        }

        // @var Activity $activity
        if (isset($data->activityId)) {
            $activity = $this->findEntity(Activity::class, $data->activityId);
        }

        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = parent::createEntity($data);
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setActivity($activity);

        return $scheduleEntry;
    }
}
