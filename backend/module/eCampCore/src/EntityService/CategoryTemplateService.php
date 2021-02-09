<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Hydrator\CategoryTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityCategoryTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CategoryTemplate::class,
            CategoryTemplateHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['campTemplateId'])) {
            $q->andWhere('row.campTemplate = :campTemplateId');
            $q->setParameter('campTemplateId', $params['campTemplateId']);
        }

        return $q;
    }
}
