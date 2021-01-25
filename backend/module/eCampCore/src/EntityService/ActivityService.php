<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Core\Entity\ScheduleEntry;
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

    /** @var ActivityContentService */
    protected $activityContentService;

    public function __construct(
        ActivityResponsibleService $activityResponsibleService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ScheduleEntryService $scheduleEntryService,
        ActivityContentService $activityContentService
    ) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );
        $this->scheduleEntryService = $scheduleEntryService;
        $this->activityResponsibleService = $activityResponsibleService;
        $this->activityContentService = $activityContentService;
    }

    /**
     * @param mixed $data
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return BaseEntity
     */
    protected function createEntityPost(BaseEntity $entity, $data) {
        /** @var Activity $activity */
        $activity = $entity;

        $this->updateActivityResponsibles($activity, $data);

        $this->updateScheduleEntries($activity, $data);

        $this->createInitialActivityContents($activity);

        return $entity;
    }

    /**
     * @param $data
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return BaseEntity
     */
    protected function patchEntity(BaseEntity $entity, $data) {
        /** @var Activity $activity */
        $activity = parent::patchEntity($entity, $data);
        $this->updateActivityResponsibles($activity, $data);
        $this->updateScheduleEntries($activity, $data);

        if (!empty($data->activityCategoryId)) {
            $category = $this->findRelatedEntity(ActivityCategory::class, $data, 'activityCategoryId');
            $activity->setActivityCategory($category);
        }

        return $entity;
    }

    protected function updateEntity(BaseEntity $entity, $data) {
        $entity = parent::updateEntity($entity, $data);
        $this->updateActivityResponsibles($entity, $data);
        $this->updateScheduleEntries($entity, $data);

        return $entity;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        if (isset($params['periodId'])) {
            $q->innerJoin('row.scheduleEntries', 'scheduleEntry');
            $q->andWhere('scheduleEntry.period = :periodId');
            $q->setParameter('periodId', $params['periodId']);
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

        /** @var ActivityCategory $category */
        $category = $this->findRelatedEntity(ActivityCategory::class, $data, 'activityCategoryId');

        $activity->setActivityCategory($category);
        $activity->setCamp($category->getCamp()); // TODO meeting discus: Why do we actually need camp on activity? Redundant relationship

        return $activity;
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

    private function updateScheduleEntries(Activity $activity, $data) {
        if (isset($data->scheduleEntries) && is_array($data->scheduleEntries)) {
            $scheduleEntryIds = array_reduce($data->scheduleEntries, function ($result, $entry) {
                if (isset($entry['id'])) {
                    $result[] = $entry['id'];
                }

                return $result;
            }, []);

            foreach ($activity->getScheduleEntries() as $scheduleEntryInDb) {
                /** @var ScheduleEntry $scheduleEntryInDb */
                if (!in_array($scheduleEntryInDb->getId(), $scheduleEntryIds)) {
                    $this->scheduleEntryService->delete($scheduleEntryInDb->getId());
                }
            }

            foreach ($data->scheduleEntries as $data) {
                $data = (object) $data;
                if (isset($data->id)) {
                    $scheduleEntry = $this->findEntity(ScheduleEntry::class, $data->id);
                    $this->scheduleEntryService->patch($scheduleEntry->getId(), $data);
                } else {
                    $data->activityId = $activity->getId();
                    $this->scheduleEntryService->create($data);
                }
            }
        }
    }

    private function createInitialActivityContents(Activity $activity) {
        $contentTypeConfigs = $activity->getActivityCategory()->getContentTypeConfigs();

        /** @var ContentTypeConfig $contentTypeConfig */
        foreach ($contentTypeConfigs as $contentTypeConfig) {
            if ($contentTypeConfig->getRequired()) {
                $this->activityContentService->create((object) [
                    'activityId' => $activity->getId(),
                    'contentTypeId' => $contentTypeConfig->getContentType()->getId(),
                ]);
            }
        }
    }
}
