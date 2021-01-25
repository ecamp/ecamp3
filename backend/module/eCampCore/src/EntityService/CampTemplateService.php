<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Hydrator\CampTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CampTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CampTemplate::class,
            CampTemplateHydrator::class,
            $authenticationService
        );
    }
}
