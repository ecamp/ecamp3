<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ActivityHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityService extends AbstractEntityService {
    private ScheduleEntryService $scheduleEntryService;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, ScheduleEntryService $scheduleEntryService) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );
        $this->scheduleEntryService = $scheduleEntryService;
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
     * @param mixed $data
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return Activity
     */
    protected function createEntity($data) {
        // @var Camp $camp
        if (isset($data->campId)) {
            $camp = $this->findEntity(Camp::class, $data->campId);
        }

        // @var ActivityCategory $category
        if (isset($data->activityCategoryId)) {
            $category = $this->findEntity(ActivityCategory::class, $data->activityCategoryId);
        }

        /** @var Activity $activity */
        $activity = parent::createEntity($data);
        $activity->setCamp($camp);
        $activity->setActivityCategory($category);

        return $activity;
    }

    protected function createEntityPost(BaseEntity $entity, $data) {
        /** @var Activity $activity */
        $activity = $entity;

        // Create Periods:
        if (isset($data->scheduleEntries)) {
            foreach ($data->scheduleEntries as $scheduleEntry) {
                /** @var ScheduleEntry $scheduleEntry */
                $scheduleEntry = (object) $scheduleEntry;
                $scheduleEntry->activityId = $activity->getId();
                $this->scheduleEntryService->create($scheduleEntry);
            }
        }

        return $activity;
    }
}
