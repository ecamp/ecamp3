<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityHydrator;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityService extends AbstractEntityService {
    /** @var ActivityResponsibleService */
    protected $activityResponsibleService;

    public function __construct(
        ActivityResponsibleService $activityResponsibleService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService
    ) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );

        $this->activityResponsibleService = $activityResponsibleService;
    }

    protected function craeteEntityPost(BaseEntity $entity, $data) {
        $this->updateActivityResponsibles($entity, $data);

        return $entity;
    }

    protected function patchEntity(BaseEntity $entity, $data) {
        $entity = parent::patchEntity($entity, $data);
        $this->updateActivityResponsibles($entity, $data);

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
}
