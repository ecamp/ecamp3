<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Hydrator\ActivityTypeContentTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class ActivityTypeContentTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityTypeContentType::class,
            ActivityTypeContentTypeHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['activityTypeId'])) {
            $q->andWhere('row.activityType = :activityTypeId');
            $q->setParameter('activityTypeId', $params['activityTypeId']);
        }

        return $q;
    }
}
