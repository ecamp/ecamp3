<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\CampTemplate;
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

    /**
     * @param $data
     *
     * @return MaterialListTemplate
     */
    protected function createEntity($data) {
        /** @var MaterialListTemplate $materialListTemplate */
        $materialListTemplate = parent::createEntity($data);

        /** @var CampTemplate $campTemplate */
        $campTemplate = $this->findRelatedEntity(CampTemplate::class, $data, 'campTemplateId');
        $campTemplate->addMaterialListTemplate($materialListTemplate);

        return $materialListTemplate;
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
