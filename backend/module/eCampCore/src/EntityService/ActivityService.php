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
    /** @var ActivityResponsibleService */
    protected $activityResponsibleService;

    /** @var ScheduleEntryService */
    protected $scheduleEntryService;

    public function __construct(
        ActivityResponsibleService $activityResponsibleService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ScheduleEntryService $scheduleEntryService
    ) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );
        $this->scheduleEntryService = $scheduleEntryService;
        $this->activityResponsibleService = $activityResponsibleService;
    }

    /**
     * @param BaseEntity $entity
     * @param mixed $data
     *
     * @return BaseEntity
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function createEntityPost(BaseEntity $entity, $data) {
        /** @var Activity $entity */
        $this->updateActivityResponsibles($entity, $data);

        // Create ScheduleEntries
        if (isset($data->scheduleEntries)) {
            foreach ($data->scheduleEntries as $scheduleEntry) {
                $scheduleEntry = (object) $scheduleEntry;
                if (isset($scheduleEntry->id)) {
                    $entity->addScheduleEntry($this->findEntity(ActivityCategory::class, $scheduleEntry->id));
                } else {
                    $scheduleEntry->activityId = $entity->getId();
                    $this->scheduleEntryService->create($scheduleEntry);
                }
            }
        }

        return $entity;
    }

    /**
     * @param BaseEntity $entity
     * @param $data
     *
     * @return BaseEntity
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function patchEntity(BaseEntity $entity, $data) {
        /** @var Activity $activity */
        $activity = parent::patchEntity($entity, $data);
        $this->updateActivityResponsibles($activity, $data);

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

        return $entity;
    }

    protected function updateEntity(BaseEntity $entity, $data) {
        $entity = parent::updateEntity($entity, $data);
        $this->updateActivityResponsibles($entity, $data);

        return $entity;
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

    private function updateActivityResponsibles(Activity $activity, $data) {
        if (isset($data->campCollaborations)) {
            $ccIds = array_map(function ($cc) {
                return $cc['id'];
            }, $data->campCollaborations);

            foreach ($activity->getActivityResponsibles() as $activityResponsible) {
                $campCollaboration = $activityResponsible->getCampCollaboration();
                if (!in_array($campCollaboration->getId(), $ccIds)) {
                    $this->activityResponsibleService->delete($activityResponsible->getId());
                }
            }

            foreach ($ccIds as $ccId) {
                $ccExists = $activity->getActivityResponsibles()->exists(function ($key, $ar) use ($ccId) {
                    return $ar->getCampCollaboration()->getId() == $ccId;
                });
                if (!$ccExists) {
                    $this->activityResponsibleService->create((object) [
                        'activityId' => $activity->getId(),
                        'campCollaborationId' => $ccId,
                    ]);
                }
            }
        }
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
}
