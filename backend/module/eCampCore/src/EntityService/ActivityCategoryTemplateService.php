<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Hydrator\ActivityCategoryTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityCategoryTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityCategoryTemplate::class,
            ActivityCategoryTemplateHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['campTemplateId'])) {
            $q->andWhere('row.campTemplate = :campTemplateId');
            $q->setParameter('campTemplateId', $params['campTemplateId']);
        }

        return $q;
    }
}
