<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
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
        $data = (object) $data;

        /** @var Activity $activity */
        $activity = parent::createEntity($data);

        if (isset($data->campId)) {
            /** @var Camp $camp */
            $camp = $this->findEntity(Camp::class, $data->campId);
            $camp->addActivity($activity);
        }

        if (isset($data->activityCategoryId)) {
            /** @var ActivityCategory $category */
            $category = $this->findEntity(ActivityCategory::class, $data->activityCategoryId);
            $activity->setActivityCategory($category);
        }

        return $activity;
    }

    /**
     * @param Activity|BaseEntity $activity
     * @param mixed               $data
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Activity|BaseEntity
     */
    protected function createEntityPost(BaseEntity $activity, $data) {
        // Create Periods:
        if (isset($data->scheduleEntries)) {
            foreach ($data->scheduleEntries as $scheduleEntry) {
                $scheduleEntry = (object) $scheduleEntry;
                if (isset($scheduleEntry->id)) {
                    $activity->addScheduleEntry($this->findEntity(ActivityCategory::class, $scheduleEntry->id));
                } else {
                    $scheduleEntry->activityId = $activity->getId();
                    $this->scheduleEntryService->create($scheduleEntry);
                }
            }
        }

        return $activity;
    }
}
