<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class EventTypePluginService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            EventTypePlugin::class,
            EventTypePluginHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['eventTypeId'])) {
            $q->andWhere('row.eventType = :eventTypeId');
            $q->setParameter('eventTypeId', $params['eventTypeId']);
        }

        return $q;
    }
}
