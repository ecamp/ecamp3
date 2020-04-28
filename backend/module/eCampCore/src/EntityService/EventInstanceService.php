<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventInstanceService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventInstance::class,
            EventInstanceHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['event_id'])) {
            $q->andWhere('row.event = :eventId');
            $q->setParameter('eventId', $params['event_id']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.event', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
