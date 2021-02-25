<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ActivityHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityService extends AbstractEntityService {
    protected ActivityResponsibleService $activityResponsibleService;
    protected ScheduleEntryService $scheduleEntryService;
    protected ContentNodeService $contentNodeService;

    public function __construct(
        ActivityResponsibleService $activityResponsibleService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ScheduleEntryService $scheduleEntryService,
        ContentNodeService $contentNodeService
    ) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );
        $this->scheduleEntryService = $scheduleEntryService;
        $this->activityResponsibleService = $activityResponsibleService;
        $this->contentNodeService = $contentNodeService;
    }

    /**
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     */
    protected function createEntity($data): Activity {
        $data = (object) $data;

        /** @var Activity $activity */
        $activity = parent::createEntity($data);

        /** @var Category $category */
        $category = $this->findRelatedEntity(Category::class, $data, 'categoryId');

        $activity->setCategory($category);
        $activity->setCamp($category->getCamp()); // TODO meeting discus: Why do we actually need camp on activity? Redundant relationship

        return $activity;
    }

    /**
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return BaseEntity
     */
    protected function createEntityPost(BaseEntity $entity, $data): Activity {
        /** @var Activity $activity */
        $activity = $entity;

        $this->updateActivityResponsibles($activity, $data);
        $this->updateScheduleEntries($activity, $data);
        $this->createInitialContentNodes($activity);

        return $activity;
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
    protected function patchEntity(BaseEntity $entity, $data): Activity {
        /** @var Activity $activity */
        $activity = parent::patchEntity($entity, $data);
        $this->updateActivityResponsibles($activity, $data);
        $this->updateScheduleEntries($activity, $data);

        if (!empty($data->categoryId)) {
            $category = $this->findRelatedEntity(Category::class, $data, 'categoryId');
            $activity->setCategory($category);
        }

        return $entity;
    }

    protected function updateEntity(BaseEntity $entity, $data): Activity {
        $entity = parent::updateEntity($entity, $data);
        $this->updateActivityResponsibles($entity, $data);
        $this->updateScheduleEntries($entity, $data);

        return $entity;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
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

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    private function updateActivityResponsibles(Activity $activity, $data): void {
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

    private function updateScheduleEntries(Activity $activity, $data): void {
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

    private function createInitialContentNodes(Activity $activity): void {
        $categoryContents = $activity->getCategory()->getRootCategoryContents();

        /** @var CategoryContent $categoryContent */
        foreach ($categoryContents as $categoryContent) {
            $this->contentNodeService->createFromCategoryContent($activity, $categoryContent);
        }
    }
}
