<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\MaterialListTemplate;
use eCamp\Core\Hydrator\MaterialListTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class MaterialListTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialListTemplate::class,
            MaterialListTemplateHydrator::class,
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
