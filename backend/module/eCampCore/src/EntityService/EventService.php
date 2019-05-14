<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\EventHydrator;
use eCamp\Core\Entity\Event;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Event::class,
            EventHydrator::class,
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
}
