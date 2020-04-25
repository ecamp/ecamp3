<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

class EventCategoryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventCategory::class,
            EventCategoryHydrator::class,
            $authenticationService
        );
    }


    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['camp_id'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['camp_id']);
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
     * @return EventCategory|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var EventCategory $eventCategory */
        $eventCategory = parent::create($data);

        /** @var EventType $eventType */
        $eventType = $this->findEntity(EventType::class, $data->event_type_id);
        $eventCategory->setEventType($eventType);

        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->camp_id);
        $camp->addEventCategory($eventCategory);

        return $eventCategory;
    }
}
