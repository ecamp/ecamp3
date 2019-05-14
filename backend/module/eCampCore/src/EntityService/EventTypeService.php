<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventType::class,
            EventTypeHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['camp_type_id'])) {
            $q->andWhere(':campTypeId MEMBER OF row.campTypes');
            $q->setParameter('campTypeId', $params['camp_type_id']);
        }

        return $q;
    }

}
