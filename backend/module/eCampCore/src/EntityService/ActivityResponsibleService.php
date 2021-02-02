<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Hydrator\ActivityResponsibleHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityResponsibleService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityResponsible::class,
            ActivityResponsibleHydrator::class,
            $authenticationService
        );
    }

    protected function createEntity($data): ActivityResponsible {
        /** @var Activity $activity */
        $activity = $this->findRelatedEntity(Activity::class, $data, 'activityId');

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $this->findRelatedEntity(CampCollaboration::class, $data, 'campCollaborationId');

        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = parent::createEntity($data);
        $activity->addActivityResponsible($activityResponsible);
        $campCollaboration->addActivityResponsible($activityResponsible);

        return $activityResponsible;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'a');
        $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('a.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'a');
        $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));

        return $q;
    }
}
