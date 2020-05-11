<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Hydrator\ActivityTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class ActivityTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityType::class,
            ActivityTypeHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['campTypeId'])) {
            $q->andWhere(':campTypeId MEMBER OF row.campTypes');
            $q->setParameter('campTypeId', $params['campTypeId']);
        }

        return $q;
    }
}
